<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order__log".
 *
 * @property int $id
 * @property int $order_id
 * @property string $action
 * @property string $result
 * @property string $info
 * @property string $time
 */
class OrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order__log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'action', 'result'], 'required'],
            [['order_id', 'user_id'], 'integer'],
            [['time', 'info', 'user_id'], 'safe'],
            [['action', 'result'], 'string', 'max' => 100],
            [['info'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'action' => 'Action',
            'result' => 'Result',
            'info' => 'Info',
            'time' => 'Time',
        ];
    }
}
