<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\behaviors\SluggableBehavior;
use yii\helpers\FileHelper;

use common\models\Product;
use common\models\Category;

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
 */
class ProductForm extends Model
{
	public $id;
	public $name_ru;
	public $name_en;
	public $slug;
	public $category_id;
	public $art;
	public $color_id;
    public $material_id;
    public $size_id;
    public $design_id;
	public $pictures;
	public $barcode;
	public $status;
    public $selfprice;
    public $price;
    public $price_final;
    public $discount;
    
    public $weight;
    public $width;
    public $height;
    public $length;
    public $quantityInbox;
    
    public $sew_base;
    public $sew_rubber;
    public $sew_thread;
    public $sew_label;
    
    public $print_type;
    public $print_colors;
    public $print_expense;
    public $print_more_material;
    public $print_panton_numbers;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_ru', 'name_en', 'art', 'color_id', 'material_id', 'size_id', 'barcode'], 'required'],
            [['name_ru', 'name_en', 'slug'], 'string', 'max' => 100],
            ['design_id', 'integer'],
            [['art'], 'string', 'max' => 20],
            [['barcode'], 'string', 'max' => 30],
            [['status'], 'string', 'max' => 1],
            
            ['slug', 'unique', 'targetClass' => '\common\models\Product', 'message' => 'Такой Slug для товара уже существует.', 'filter' => function ($query) {
                if ($this->id) {
					$query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
            
            [['selfprice', 'price', 'price_final'], 'number'],
            
            ['pictures', 'file', 'extensions' => 'png, jpg, gif', 'skipOnEmpty' => true, 'maxFiles' => 7, 'checkExtensionByMimeType'=>false],
            
			[['id', 'category_id', 'discount'], 'safe'],
            
            [['weight', 'width', 'height', 'length', 'quantityInbox', 'selfprice', 'price', 'discount', 'weight', 'width', 'height', 'length', 'sew_base', 'sew_rubber', 'sew_thread', 'print_expense'], 'number'],
            
            [['sew_label'], 'integer'],
            [['print_type', 'print_colors'], 'string', 'max' => 4],
            [['print_more_material', 'print_panton_numbers'], 'string', 'max' => 70]
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
            'art' => 'Артикул',
            'color_id' => 'Цвет',
            'material_id' => 'Материал',
            'size_id' => 'Размер',
            'pictures' => 'Изображения товара',
            'barcode' => 'Штрихкод',
            'status' => 'Статус',
            'selfprice' => 'Себестоимость',
            'price' => 'Цена без скидки',
            'price_final' => 'Итоговая цена',
            'weight' => 'Вес брутто единицы товара, кг', 
            'width' => 'Ширина единицы товара, см', 
            'height' => 'Высота единицы товара, см', 
            'length' => 'Длина единицы товара, см', 
            'quantityInbox' => 'Штук в коробке',
            'design_id' => 'Принт',
            'sew_base' => 'Основа',
            'sew_rubber' => 'Резинка',
            'sew_thread' => 'Нитки',
            'sew_label' => 'Бирки',
            'print_type' => 'Тип печати',
            'print_colors' => 'Кол-во цветов',
            'print_expense' => 'Расход краски',
            'print_more_material' => 'Еще материал',
            'print_panton_numbers' => 'Номера пантонов',
        ];
    }
	
	public function saveProduct()
    {
		if (!$this->validate()) {
            return null;
        }
		
        if ($this->id) {
            $p = Product::findOne(['id' => $this->id]);
        } else {
            $p = new Product;
        }
        
        if (empty($this->slug)) {
			$this->slug = \yii\helpers\Inflector::slug($this->name_ru, '-');
		}
        
		$p->name_ru = $this->name_ru;
		$p->name_en = $this->name_en;
        $p->slug = $this->slug;
        $p->art = $this->art;
        $p->category_id = $this->category_id;
        $p->material_id = $this->material_id;
        $p->color_id = $this->color_id;
        $p->size_id = $this->size_id;
        $p->design_id = $this->design_id;
        $p->barcode = $this->barcode;
        $p->status = $this->status;
		$p->picture = 0;
		
        $p->selfprice = $this->selfprice;
        $p->price = $this->price;
        $p->discount = $this->price > 0 ? 100 - ($this->price_final * 100 / $this->price) : 0;
        
        $p->weight = $this->status;
        $p->width = $this->width;
        $p->height = $this->height;
        $p->length = $this->length;
        $p->quantityInbox = $this->quantityInbox;
        
        $p->sew_base = $this->sew_base;
        $p->sew_rubber = $this->sew_rubber;
        $p->sew_thread = $this->sew_thread;
        $p->sew_label = $this->sew_label;
        $p->print_type = $this->print_type;
        $p->print_colors = $this->print_colors;
        $p->print_expense = $this->print_expense;
        $p->print_more_material = $this->print_more_material;
        $p->print_panton_numbers = $this->print_panton_numbers;
        
		$p->save();
		
        /**
         * загружаем картинки
         */
        if (!empty($this->pictures)) 
        {
            $uploadDir = '/uploads/' . date('Y/m/d/');

            FileHelper::createDirectory(Yii::getAlias('@frontend') . '/web' . $uploadDir);

            foreach ($this->pictures AS $f) {
                $path = $uploadDir . $f->baseName . '_' . rand(1, 999) . '.' . $f->extension;
                $f->saveAs(Yii::getAlias('@frontend') . '/web' . $path);
                $p->addPicture($path);
            }
        }
        
		return $p->id > 0 ? $p : null;
	}
}
