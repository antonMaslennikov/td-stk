<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "production__items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $quantity
 */
class ProductionItems extends \yii\db\ActiveRecord
{
    const STATUS_ACCEPTED = 1;
    const STATUS_PRINTING = 2;
    const STATUS_READY = 3;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'production__items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'item_id', 'status', 'quantity'], 'required'],
            [['order_id', 'quantity', 'product_id'], 'integer'],
            [['printed_at', 'sewing_at', 'reserved_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'quantity' => 'Quantity',
        ];
    }
    
    /**
     * Получить количество позиций уже в производстве
     * @param  [[Type]] $item_id [[Description]]
     */
    public static function getQuantityInProduction($item_id, $status = null)
    {
        $q = (new \yii\db\Query())
            ->select('sum(`quantity`) AS c')
            ->from(self::tableName())
            ->andWhere(['and', 'item_id = :id'], [':id' => $item_id]);
        
        if ($status) {
            $q->andFilterWhere(['and', 'status' => $status]);
        }
           
        $c = $q->one(); 

        return (int) $c['c'];
    }

    /**
     * Переместить позицию на производстве на этап печати
     */
    public function move2printing()
    {
        $this->sewing_at = date('Y-m-d H:i:s', time());
        $this->status = self::STATUS_PRINTING;
        
        return $this->save() ? $this : false;
    }
    
    /**
     * Отправить позицию на производстве в резерв на склад
     */
    public function move2reserv()
    {
        if ($this->status == self::STATUS_READY) {
            return false;
        }
        
        $this->reserved_at = date('Y-m-d H:i:s', time());
        $this->status = self::STATUS_READY;
        $this->save();
        
        // заводим необходимое количество поизций на складе
        // и резервируем их за позицией в заказе
        for ($i = 0; $i < $this->quantity; $i++) 
        {
            $si = new StockItem;
            $si->status = 1;
            $si->order_item_id = $this->item_id;
            $si->product_id = $this->product_id;
            $si->save();
        }
        
        return true;
    }
}