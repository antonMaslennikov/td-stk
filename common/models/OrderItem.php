<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order__item".
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property double $price
 * @property double $discount
 */
class OrderItem extends \yii\db\ActiveRecord
{
    protected $_priceold;
    protected $_discountold;
    protected $_quantityold;
    protected $_is_deleted;
        
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order__item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'quantity', 'price'], 'required'],
            [['order_id', 'product_id', 'quantity'], 'integer'],
            ['is_deleted', 'in', 'range' => [0, 1]],
            [['price', 'discount'], 'number'],
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
            'product_id' => 'Product ID',
            'quantity' => 'Quanity',
            'price' => 'Sum',
            'discount' => 'Скидка',
        ];
    }
    
    public function getProduct(){
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    
    public function getOrder(){
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
    public function afterFind()
    {
        $this->_priceold = $this->price;
        $this->_discountold = $this->discount;
        $this->_quantityold = $this->quantity;
        $this->_is_deleted = $this->is_deleted;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($this->price != $this->_priceold || $this->_discountold != $this->discount || $this->_quantityold != $this->quantity || $this->_is_deleted != $this->is_deleted) {
            
            $q = (new \yii\db\Query())
                ->select('SUM((`price` * (1 - `discount` / 100)) * `quantity`) AS sum')
                ->from(self::tableName())
                ->where(['and', ['order_id' => $this->order_id], 'id != ' . $this->id, ['is_deleted' => 0]])
                ->one(); 
            
            if ($this->_is_deleted == $this->is_deleted) {
                $q['sum'] += ($this->price * (1 - $this->discount / 100)) * $this->quantity;
            }
            
            // специально не через эктив_рекорд чтобы не вызвать обновление модели
            Yii::$app->db
                    ->createCommand()
                    ->update(Order::tableName(), ['sum' => $q['sum'], 'payment_confirm' => $q['sum'] + $this->order->delivery_cost > $this->order->alreadyPayed ? 0 : 1], 'id=:id', array(':id' => (int) $this->order_id))
                    ->execute();
                       
            if ($this->price != $this->_priceold) {
                $this->order->log('edit_price', $this->price - $this->_priceold, $this->id);
            }
            
            if ($this->_discountold != $this->discount) {
                $this->order->log('edit_discount', $this->discount - $this->_discountold, $this->id);
            }
            
            if ($this->_quantityold != $this->quantity) {
                $this->order->log('edit_quantity', $this->quantity - $this->_quantityold, $this->id);
            }
            
            if ($this->_is_deleted != $this->is_deleted) {
                $this->order->log('del_position', $this->id);
            }
        }
    }
}
