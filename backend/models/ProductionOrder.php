<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "production__order".
 *
 * @property int $id
 * @property int $order_id
 * @property string $created_at
 */
class ProductionOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'production__order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['created_at'], 'safe'],
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
            'created_at' => 'Created At',
        ];
    }
    
    public function getItems(){
        return $this->hasMany(ProductionItems::className(), ['order_id' => 'id']);
    }
}
