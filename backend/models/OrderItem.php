<?php

namespace backend\models;

use Yii;

use backend\models\ProductionOrder;
use backend\models\ProductionItems;

class OrderItem extends \common\models\OrderItem
{
    /**
     * Отправить готовую позицию на складе в резерв
     */
    public function put2reserv()
    {
        $reserved = (new \yii\db\Query())
                        ->select('count(*) AS c')
                        ->from(StockItem::tableName())
                        ->where(['order_item_id' => $this->id, 'product_id' => $this->product_id])
                        ->one();
        
        $limit = $this->quantity - $reserved['c'];
        
        Yii::$app->db->createCommand('update {{' . StockItem::tableName() . '}} set order_item_id = ' . $this->id . ' where product_id = ' . $this->product_id . ' and order_item_id = 0 LIMIT ' . $limit)->execute();
                //->update(StockItem::tableName(), ['order_item_id' => $this->id], ['product_id' => $this->product_id, 'order_item_id' => 0])
                //->execute();
    }
    
    /**
     * Отправить позицию в производство
     */
    public function put2production()
    {
        // вычисляем общее количество готовых позиций на складе
        $ready = Stock::getReadyProductQuantity($this->product_id, 0);
        
        // вычисляем количество готовых позиций на складе и зарезервированных за этой позицией
        $readyReserved = Stock::getReadyProductQuantity($this->product_id, $this->id);
        
        // если есть готовые позиции на складе запрещаем ставить в производство до тех пор пока их не поставять в резерв
        if ($ready > 0 && $readyReserved == 0) {
            Yii::$app->session->setFlash('warning', 'На складе есть готовые позиции. Установите их в резерв');
            return null;
        }
        
        // если нет то создаём заказ на производство
        if (!$pr_order = ProductionOrder::find()->where(['order_id' => $this->order_id])->one()) {
            
            $pr_order = new ProductionOrder;
            
            $pr_order->order_id = $this->order_id;
            $pr_order->save();
        }
        
        // вычисляем количество позиций которые уже находяться в производстве но ещё не пошиты
        $inproduction = ProductionItems::getQuantityInProduction($this->id, ProductionItems::STATUS_ACCEPTED);
             
        // если количество позиции меньше чем уже есть готового на складе и зарезервировано за заказом и количества в производстве
        if ($this->quantity - $readyReserved - $inproduction > 0) 
        {
            $pr_item = new ProductionItems;
            $pr_item->order_id = $pr_order->id;
            $pr_item->item_id = $this->id;
            $pr_item->quantity = $this->quantity - $readyReserved - $inproduction;
            $pr_item->status = ProductionItems::STATUS_ACCEPTED;
            $pr_item->save();
            
            return true;
        }
    }
}