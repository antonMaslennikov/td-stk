<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order__address".
 *
 * @property int $id
 * @property int $county
 * @property int $city
 * @property string $address
 */
class OrderAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order__address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'client_id'], 'required'],
            [['country', 'city', 'name', 'phone'], 'safe'],
            [['country', 'city', 'client_id'], 'integer'],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'County',
            'city' => 'City',
            'address' => 'Address',
        ];
    }
}
