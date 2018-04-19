<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\OrderComments;

/**
 *
 */
class OrderCommentForm extends Model
{
	public $order_id;
    public $text;
    public $for;
    
    const FOR_ADMIN = 0;
    const FOR_CLIENT = 1;
    
    public function rules()
    {
        return [
            [['text', 'order_id'], 'required'],
            ['text', 'string', 'max' => 1000],
            ['for', 'in', 'range' => array_keys(self::getForList())],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'for' => 'Для кого',
            'text' => 'Комментарий',
        ];
    }
    
    public function getForList() {
        return [
            self::FOR_ADMIN => 'Для администратора',
            self::FOR_CLIENT => 'Для клиента',
        ];
    }
	
	public function addComment()
    {
        if (!$this->validate()) {
			foreach ($this->getErrors() as $key => $value) {
				Yii::$app->session->setFlash('error', $value[0]);
			}
            return null;
        }
        
        $C = new OrderComments;
        
        $C->order_id = $this->order_id;
        $C->text = $this->text;
        $C->for = $this->for;
        $C->user_id = Yii::$app->user->getId();
        
        return $C->save() ? $C : null;
    }
}