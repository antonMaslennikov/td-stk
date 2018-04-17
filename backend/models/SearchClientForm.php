<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 *
 */
class SearchClientForm extends Model
{
	public $client_id;
    
    public function rules()
    {
        return [
            ['client_id', 'required'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Клиент',
        ];
    }
}