<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "document".
 *
 * @property int $id
 * @property int $type
 * @property int $parent
 * @property string $direction
 * @property int $name
 * @property int $number
 * @property string $date
 * @property int $order_id
 * @property int $client_id
 * @property int $sum
 * @property int $sum_payed
 * @property int $payed
 * @property string $payment_type
 */
class Document extends \yii\db\ActiveRecord
{
    const TYPE_BILL = 1;
    const TYPE_AKT  = 2;
    const TYPE_NAKL = 3;
    
    const FOR_SUP = 0;
    const FOR_CLIENT = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document';
    }
    
    public static function getTypes()
    {
        return [
            self::TYPE_BILL => 'Счёт',
            self::TYPE_AKT  => 'Акт',
            self::TYPE_NAKL => 'Накладная',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'number', 'client_id'], 'required'],
            [['order_id'], 'integer'],
            ['type', 'default', 'value' => Document::TYPE_BILL],
            ['direction', 'default', 'value' => Document::FOR_CLIENT],
            [['date', 'type', 'direction','payment_type', 'sum', 'positions'], 'safe'],
            [['name', 'number'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'parent' => 'Parent',
            'direction' => 'Direction',
            'name' => 'Название',
            'number' => 'Номер',
            'date' => 'Дата',
            'order_id' => 'Заказ',
            'sum' => 'Сумма',
            'sum_payed' => 'Сумма оплачено',
            'payed' => 'Оплачено',
            'payment_type' => 'Тип оплаты',
        ];
    }
    
    public function getPositions(){
        return $this->hasMany(DocumentPosition::className(), ['document_id' => 'id']);
    }
}
