<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "product__pictures".
 *
 * @property int $id
 * @property int $product_id
 * @property string $path
 * @property string $thumb
 * @property string $time
 * @property int $main
 */
class Picture extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product__pictures';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'path'], 'required'],
            [['product_id', 'main'], 'integer'],
            [['time', 'time', 'thumb', 'main'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'path' => 'Path',
            'thumb' => 'Thumb',
            'time' => 'Time',
            'main' => 'Основная',
        ];
    }
}
