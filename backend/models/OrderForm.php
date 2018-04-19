<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\Order;
use common\models\OrderAddress;

/**
 *
 */
class OrderForm extends Model
{
	public $id;
    public $client;
    public $client_id;
    public $address_id;
    public $address;
    public $payment_type;
    public $delivery_type;
    public $delivery_cost = 0;
    
    public $addressList = [
        -1 => 'Новый адрес',
    ];
    
    public function rules()
    {
        return [
            ['address', 'string', 'max' => 255],
            [['client_id', 'payment_type', 'delivery_type'], 'required'],
            [['client_id', 'address_id'], 'integer'],
            ['payment_type', 'in', 'range' => array_keys(Order::$paymentTypes)],
            ['delivery_type', 'in', 'range' => array_keys(Order::$deliveryTypes)],
            [['address_id'], 'safe'],
            [['delivery_cost'], 'number', 'min' => 0],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Order::attributeLabels(), [
            'address' => 'Укажите адрес доставки'
        ]);
    }
    
    public function getAddressList()
    {
        if ($this->client->id > 0) {
            $addresses = OrderAddress::find()
                ->select(['address', 'id'])
                ->where(['client_id' => $this->client->id])
                ->indexBy('id')
                ->orderBy(['id' => SORT_DESC])
                ->column();
            
            $this->addressList = ArrayHelper::merge((array) $addresses, $this->addressList);
        }
        
        return $this->addressList;
    }
	
	public function createOrder()
    {
        if (!$this->validate()) {
            return null;
        }
        
        if ($this->address_id > 0) {
        } else {
            $address = new OrderAddress;
            $address->address = $this->address;
            $address->client_id = $this->client->id;
            $address->save();
        
            $this->address_id = $address->id;
        }
        
        $O = new Order;
        $O->status        = Order::STATUS_ORDERED;
        $O->client_id     = $this->client->id;
        $O->payment_type  = $this->payment_type;
        $O->delivery_type = $this->delivery_type;
        $O->delivery_cost = $this->delivery_cost;
        $O->address_id    = $this->address_id;
        $O->sum = 0;
        $O->manager_id    = \Yii::$app->user->identity->getId();
            
        return $O->save() ? $O : null;
    }
}