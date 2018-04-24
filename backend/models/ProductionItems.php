<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "production__items".
 *
 * @property int $id
 * @property int $order_id
 * @property int $quantity
 */
class ProductionItems extends \yii\db\ActiveRecord
{
    const STATUS_ACCEPTED = 1;
    const STATUS_READY = 3;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'production__items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'item_id', 'status', 'quantity'], 'required'],
            [['order_id', 'quantity'], 'integer'],
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
            'quantity' => 'Quantity',
        ];
    }
    
    /**
     * Получить количество позиций уже в производстве
     * @param  [[Type]] $item_id [[Description]]
     */
    public static function getQuantityInProduction($item_id, $status = null)
    {
        $q = (new \yii\db\Query())
            ->select('sum(`quantity`) AS c')
            ->from(self::tableName())
            ->andWhere(['and', 'item_id = :id'], [':id' => $item_id]);
        
        if ($status) {
            $q->andFilterWhere(['and', 'status' => $status]);
        }
           
        $c = $q->one(); 

        return (int) $c['c'];
    }
}
