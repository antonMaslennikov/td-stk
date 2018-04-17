<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\Order;
use common\models\OrderComments;

/**
 *
 */
class OrderPaymentForm extends Model
{
	public $order_id;
    public $payment_type;
    public $sum;
    
    public function rules()
    {
        return [
            [['payment_type', 'order_id', 'sum'], 'required'],
            ['sum', 'number'],
            ['payment_type', 'in', 'range' => array_keys(Order::$paymentTypes)],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sum' => 'Оплатить заказ'
        ];
    }
    
	public function setPayment()
    {
        if (!$this->validate()) {
			foreach ($this->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
            return null;
        }
        
        $O = Order::find()->where(['id' => $this->order_id])->one();
        
        if ($O->payment_confirm == 0)
        {
            $O->log('set_payment', $this->sum, $this->payment_type);
            
            if ($O->alreadyPayed >= $O->sum + $O->delivery_cost) {
                $O->payment_confirm = 1;
                $O->save();
            }
            
            Yii::$app->session->setFlash('success', 'Заказ оплачен на ' . $this->sum . ' р.');
        }
        
        return true;
    }
}