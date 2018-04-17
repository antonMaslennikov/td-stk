<?php

namespace common\models;

use Yii;
use yii\imagine\Image;

use backend\models\Size;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name_ru
 * @property string $name_en
 * @property string $slug
 * @property string $art
 * @property int $color_id
 * @property int $picture
 * @property string $barcode
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_en'], 'required'],
            ['color_id', 'integer'],
            [['name_ru', 'name_en', 'slug'], 'string', 'max' => 100],
            [['art'], 'string', 'max' => 20],
            [['barcode'], 'string', 'max' => 30],
            [['status'], 'integer', 'max' => 1],
			[['category', 'art', 'picture', 'barcode', 'created_at', 'updated_at'], 'safe'],
            [['selfprice', 'price', 'discount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_ru' => 'Название (рус)',
            'name_en' => 'Название (анг)',
            'slug' => 'Slug',
			'category' => 'Категория',
            'art' => 'Артикул',
            'color_id' => 'Цвет',
            'picture' => 'Picture ID',
            'barcode' => 'Штрихкод',
            'status' => 'Статус',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	public function getStatusList()
	{
		return [
			self::STATUS_ACTIVE => 'Включен',
			self::STATUS_DISABLED => 'Отключен', 
		];
	}
    
    public function addPicture($path)
    {
        $pic = new Picture;
        
        $pic->product_id = $this->id;
        $pic->path = $path;
        
        $thumb = explode('.', $path);
        $ext = array_pop($thumb);
        array_push($thumb, 'thumb');
        array_push($thumb, $ext);
        $pic->thumb = implode('.', $thumb);
        
        Image::thumbnail(Yii::getAlias('@frontend') . '/web' . $pic->path, \Yii::$app->params['product']['ThumbWidth'], null)
            ->save(Yii::getAlias('@frontend') . '/web' . $pic->thumb, ['quality' => 90]);
        
        $pic->save();
    }
    
    public function getCategory(){
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    
    public function getMaterial(){
        return $this->hasOne(Material::className(), ['id' => 'material_id']);
    }
    
    public function getSize(){
        return $this->hasOne(Size::className(), ['id' => 'size_id']);
    }
    
    public function getColor(){
        return $this->hasOne(Color::className(), ['id' => 'color_id']);
    }
    
    public function getPictures(){
        return $this->hasMany(Picture::className(), ['product_id' => 'id']);
    }
}
