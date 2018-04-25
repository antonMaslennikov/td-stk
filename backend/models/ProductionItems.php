<?php

namespace backend\models;

use Yii;
use common\models\Product;

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
            [['order_id', 'quantity', 'product_id', 'quantity_from_stock'], 'integer'],
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
    
    public function getProduct(){
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
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
    
    public function takefromclear()
    {
        if ($this->product_id == 0) {
            Yii::$app->session->setFlash('warning', 'Это чистая позиция. она не не нуждается в замене');
        }
        
        $clearFromStock = Yii::$app->db
            ->createCommand("update product__stock s, product p 
              set 
                s.order_item_id = :item_id
              where 
                    p.id = s.product_id
                and s.order_item_id = 0
                and p.category_id = :cat
                and p.color_id = :color
                and p.size_id = :size
                and p.design_id = 0", [':cat' => $this->product->category_id, ':color' => $this->product->color_id, 'size' => $this->product->size_id, 'item_id' => $this->item_id])
            ->execute();
        
        $this->quantity_from_stock = $clearFromStock;
        $this->save();
        
        Yii::$app->session->setFlash('success', 'Чистые позиции взяты со склада ' . $clearFromStock . ' шт.');
    }
}