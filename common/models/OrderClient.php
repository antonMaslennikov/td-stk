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
 * @property string $org
 * @property string $bank
 * @property string $bik
 * @property string $ks
 * @property string $rs
 * @property string $kpp
 * @property string $inn
 * @property string $dir
 * @property string $address
 * @property string $orgn
 * @property string $okpo
 * @property string $okato
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
            [['created_at', 'org', 'bank', 'bik', 'ks', 'rs', 'kpp', 'inn', 'dir', 'address', 'orgn', 'okpo', 'okato'], 'safe'],
            [['name', 'email', 'phone'], 'string', 'max' => 255],
            [['org', 'bank'], 'string', 'max' => 150],
            [['bik', 'inn', 'okpo', 'okato'], 'string', 'max' => 30],
            [['ks', 'rs', 'kpp', 'orgn'], 'string', 'max' => 40],
            [['dir'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'org' => 'Организация',
            'bank' => 'Банк',
            'bik' => 'БИК',
            'ks' => 'Кор. счёт',
            'rs' => 'Расчтёный счёт',
            'kpp' => 'КПП',
            'inn' => 'ИНН',
            'dir' => 'Директор',
            'address' => 'Фактический адрес',
            'orgn' => 'ОГРН',
            'okpo' => 'ОКПО',
            'okato' => 'ОКАТО',
            'created_at' => 'Дата создания',
        ];
    }
}
