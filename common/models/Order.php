<?php

namespace common\models;

use Yii;

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
class Order extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE    = 'active';
    const STATUS_ORDERED   = 'ordered';
    const STATUS_PREPARED  = 'prepared';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELED  = 'canceled';
    
    public static $paymentTypes = [
        1 => ['name' => 'Авансовый платёж'],
        2 => ['name' => 'Банковский перевод'],
        3 => ['name' => 'Наличные'],
        //4 => ['name' => 'Кредитная карта'],
    ];
        
    public static $deliveryTypes = [
        1 => ['name' => 'Транспортная компания'],
        2 => ['name' => 'Самовывоз'],
    ];
    
    public $status_name;
    public $delivery_type_rus;
    public $payment_type_rus;
        
        
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'payment_type', 'delivery_type'], 'required'],
            [['client_id', 'address_id'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            ['payment_type', 'in', 'range' => array_keys(self::$paymentTypes)],
            ['delivery_type', 'in', 'range' => array_keys(self::$deliveryTypes)],
            [['address_id', 'payment_confirm'], 'safe'],
            [['sum', 'delivery_cost'], 'number', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Клиент',
            'address_id' => 'Адрес доставки',
            'payment_type' => 'Тип оплаты',
            'payment_confirm' => 'Заказ оплачен',
            'delivery_type' => 'Тип доставки',
            'delivery_cost' => 'Доставка, руб.',
            'delivery_date' => 'Дата доставки',
            'sum' => 'Сумма, руб.',
            'created_at' => 'Создан',
            'manager' => 'Менеджер',
        ];
    }
    
    public function getItems(){
        return $this
                ->hasMany(OrderItem::className(), ['order_id' => 'id'])
                ->where(['is_deleted' => 0]);
    }
    
    public function getLogs(){
        return $this
                ->hasMany(OrderLog::className(), ['order_id' => 'id'])
                ->orderBy(['id' => SORT_DESC]);
    }
    
    public function getComments(){
        return $this->hasMany(OrderComments::className(), ['order_id' => 'id']);
    }
    
    public function getAddress(){
        return $this->hasOne(OrderAddress::className(), ['id' => 'address_id']);
    }
    
    public function getClient(){
        return $this->hasOne(OrderClient::className(), ['id' => 'client_id']);
    }
    
    public function afterFind()
    {
        $this->status_name = self::getStatusList()[$this->status];
        $this->delivery_type_rus = self::$deliveryTypes[$this->delivery_type]['name'];
        $this->payment_type_rus = self::$paymentTypes[$this->payment_type]['name'];
    }
    
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE    => 'не сформирован',
            self::STATUS_ORDERED   => 'заказан',
            self::STATUS_PREPARED  => 'подготовлен',
            self::STATUS_DELIVERED => 'доставлен',
            self::STATUS_CANCELED  => 'отменён',
        ];
    }
    
    public function getAlreadyPayed()
    {
        $p = (new \yii\db\Query())
            ->from(OrderLog::tableName())
            ->andWhere(['action' => 'set_payment'])
            ->andWhere(['order_id' => $this->id])
            ->sum('result');
        
        return (int) $p;
    }
    
    public function log($a, $r, $i = null)
    {
        $l = new OrderLog;
        
        $l->order_id = $this->id;
        $l->action = $a;
        $l->result = (string) $r;
        $l->info = (string) $i;
        $l->user_id = Yii::$app->user->getId();
        $l->save();
        
        return true;
    }
    
    public function changeStatus($status)
    {
        if (!in_array($status, array_keys(Order::getStatusList()))) {
            throw new Exception('Недопустимый статус заказа');
        }
        
        $this->status = $status;
        
        if ($status == self::STATUS_DELIVERED) {
            $this->delivered_date = date('Y-m-d H:i:s');
        }
        
        $this->save();
    }
}
