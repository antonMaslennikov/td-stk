<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

use backend\models\Document;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $client_id
 * @property int $address_id
 * @property int $payment_type
 * @property int $delivery_type
 * @property string $created_at
 */
class Order extends \common\models\Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(\common\models\Order::rules(), [
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(\common\models\Order::attributeLabels(), [
        ]);
    }
    
    public static function getPTList()
    {
        return ArrayHelper::getColumn(self::$paymentTypes, 'name');
    }
    
    public static function getDTList()
    {
        return ArrayHelper::getColumn(self::$deliveryTypes, 'name');
    }
    
    public function getClientBills(){
        return $this
                ->hasMany(Document::className(), ['order_id' => 'id'])
                ->where(['type' => Document::TYPE_BILL, 'direction' => Document::FOR_CLIENT]);
    }
}
