<?php

namespace backend\models;

use Yii;

use common\models\Product;

/**
 * This is the model class for table "product_stock".
 *
 * @property int $id
 * @property int $product_id
 * @property int $status
 * @property int $order_item_id
 * @property string $come_at
 */
class StockItem extends \yii\db\ActiveRecord
{
    public static $statuses = [
        0 => 'Свободна',
        1 => 'В резерве',
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product__stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'order_item_id'], 'integer'],
            [['come_at'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::$statuses)],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'status' => 'Status',
            'order_item_id' => 'Order Item ID',
            'come_at' => 'Come At',
        ];
    }
    
    public function getProduct(){
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    
    public function getOrderItem(){
        return $this->hasOne(OrderItem::className(), ['id' => 'order_item_id']);
    }
}
