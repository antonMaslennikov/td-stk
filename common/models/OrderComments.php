<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order__comments".
 *
 * @property int $id
 * @property int $for
 * @property int $order_id
 * @property string $text
 * @property string $time
 */
class OrderComments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order__comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['for', 'order_id', 'text'], 'required'],
            [['order_id'], 'integer'],
            [['text'], 'string'],
            [['time'], 'safe'],
            [['for'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'for' => 'For',
            'order_id' => 'Order ID',
            'text' => 'Text',
            'time' => 'Time',
        ];
    }
}
