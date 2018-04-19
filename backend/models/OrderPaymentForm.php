<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 *
 */
class OrderPaymentForm extends Model
{
	public $order_id;
    public $payment_type;
    
    public function rules()
    {
        return [
            [['payment_type', 'order_id'], 'required'],
            ['payment_type', 'in', 'range' => array_keys(Order::$paymentTypes)],
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
        
        $prev_pt = $O->payment_type;
        
        $O->payment_type = $this->payment_type;
        $O->save();
        
        if ($prev_pt != $this->payment_type) {
            $O->log('change_payment_type', $this->payment_type, $prev_pt);
        }
        
        return $O->id;
    }
}