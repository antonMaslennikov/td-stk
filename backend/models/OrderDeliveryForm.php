<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 */
class OrderDeliveryForm extends Model
{
	public $order_id;
    public $delivery_type;
    public $delivery_date;
    public $country;
    public $city;
    public $address;
    public $delivery_cost;
    
    public function rules()
    {
        return [
            [['delivery_type', 'order_id'], 'required'],
            ['delivery_type', 'in', 'range' => array_keys(Order::$deliveryTypes)],
            [['delivery_date', 'country', 'city', 'address', 'delivery_cost'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
    public function saveData()
    {
        if (!$this->validate()) {
            foreach ($this->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
            return null;
        }
        
        $O = Order::findOne($this->order_id);
        
        $prev_dt = $O->delivery_type;
        $prev_dc = $O->delivery_cost;
        $prev_dd = $O->delivery_date;
        
        $O->delivery_type = $this->delivery_type;
        $O->delivery_cost = $this->delivery_cost;
        $O->delivery_date = $this->delivery_date;
        $O->save();
        
        if ($prev_dt != $this->delivery_type) {
            $O->log('change_delivery_type', $this->delivery_type, $prev_dt);
        }
        
        if ($prev_dd != $this->delivery_date) {
            $O->log('change_delivery_date', $this->delivery_date, $prev_dd);
        }
        
        if ($prev_dc != $this->delivery_cost) {
            $O->log('change_delivery_cost', $this->delivery_cost, $prev_dc);
        }
        
        $O->address->country = $this->country;
        $O->address->city = $this->city;
        $O->address->address = $this->address;
        $O->address->save();
        
        return $O->id;
    }
}