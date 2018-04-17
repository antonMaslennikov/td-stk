<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "document__position".
 *
 * @property int $id
 * @property string $name
 * @property int $product_id
 * @property int $document_id
 * @property double $price
 * @property double $quantity
 */
class DocumentPosition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'document__position';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'document_id', 'price', 'quantity'], 'required'],
            [['id', 'product_id', 'document_id'], 'integer'],
            [['price', 'quantity'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'product_id' => 'Product ID',
            'document_id' => 'Document',
            'price' => 'Price',
            'quantity' => 'Quantity',
        ];
    }
}
