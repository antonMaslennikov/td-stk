<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\OrderClient;

/**
 *
 */
class OrderClientForm extends Model
{
	public $id;
    public $name;
    public $email;
    public $phone;
    
    public function rules()
    {
        return [
            ['email', 'unique', 'targetClass' => '\common\models\OrderClient', 'message' => 'Этот адрес электронной почты уже занят.'],
            ['phone', 'unique', 'targetClass' => '\common\models\OrderClient', 'message' => 'Этот телефон уже занят.'],
            ['name', 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(OrderClient::attributeLabels(), [
          
        ]);
    }
	
	public function saveClient()
    {
        if (!$this->validate()) {
			foreach ($this->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
            return null;
        }
        
        $client = new OrderClient;
        
        $client->name = $this->name;
        $client->email = $this->email;
        $client->phone = $this->phone;
        
        return $client->save() ? $client : null;
    }
}