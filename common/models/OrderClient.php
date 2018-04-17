<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order__client".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $created_at
 */
class OrderClient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order__client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone'], 'required'],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'created_at' => 'Создан',
        ];
    }
}
