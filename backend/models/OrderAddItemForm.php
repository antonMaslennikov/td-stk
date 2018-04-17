<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\OrderItem;

/**
 *
 */
class OrderAddItemForm extends Model
{
	public $order;
    public $order_id;
    public $product;
    public $product_id;
    public $quantity = 1;
    public $price;
    public $discount = 0;
    
    public function rules()
    {
        return [
            [['product_id', 'quantity', 'price', 'order_id'], 'required'],
            ['product_id', 'integer'],
            ['discount', 'default', 'value' => 0],
            ['discount', 'double', 'max' => 100],
            ['price', 'double'],
            [['product'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product' => 'Товар',
            'quantity' => 'Количество',
            'price' => 'Цена',
            'discount' => 'Скидка',
        ];
    }
	
	public function addItem()
    {
        if (!$this->validate()) {
            foreach ($this->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
            return null;
        }
        
        //$this->order = Order::find()->where(['id' => $this->order_id])->one();
        
        $item = new OrderItem;
        $item->order_id = $this->order_id;
        $item->product_id = $this->product_id;
        $item->quantity = $this->quantity;
        $item->price = $this->price;
        $item->discount = $this->discount;
     
        return $item->save() ? $item : null;
    }
}