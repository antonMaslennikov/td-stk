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
 * @property int $weight
 * @property string $created_at
 * @property string $updated_at
 */
class Product extends \yii\db\ActiveRecord
{
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;
    
    const SEX_MALE = 0;
    const SEX_FEMALE = 1;
    const SEX_KIDS = 2;
    const SEX_UNISEX = 3;
	
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
            [['weight', 'width', 'height', 'length', 'quantityInbox'], 'number'],
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
            'category_id' => 'Категория',
			'category' => 'Категория',
            'art' => 'Артикул',
            'color_id' => 'Цвет',
            'size_id' => 'Размер',
            'barcode' => 'Штрихкод',
            'picture' => 'Изображение',
            'status' => 'Статус',
            'created_at' => 'Дата создания',
            'updated_at' => 'Последнее обновление',
            'quantity' => 'Количество на складе',
            'weight' => 'Вес брутто единицы товара, кг', 
            'width' => 'Ширина единицы товара, см', 
            'height' => 'Высота единицы товара, см', 
            'length' => 'Длина единицы товара, см', 
            'quantityInbox' => 'Штук в коробке',
        ];
    }
	
	public function getStatusList()
	{
		return [
			self::STATUS_ACTIVE => 'Включен',
			self::STATUS_DISABLED => 'Отключен', 
		];
	}
    
    public function getSexList()
    {
        return [
			self::SEX_MALE => 'Мужское',
			self::SEX_FEMALE => 'Женское', 
            self::SEX_KIDS => 'Детское', 
            self::SEX_UNISEX => 'Унисекс', 
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
        
        if (empty($this->picture)) {
            $this->picture = $pic->thumb;
            $this->save();
        }
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
    
    public function getStockitems(){
        return $this->hasMany(\backend\models\StockItem::className(), ['product_id' => 'id']);
    }
}
