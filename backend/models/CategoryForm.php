<?php
namespace backend\models;

use yii\base\Model;
use yii\behaviors\SluggableBehavior;

use common\models\Category;

/**
 * Signup form
 */
class CategoryForm extends Model
{
	public $id;
    public $name;
    public $parent;
	public $slug;
	public $status;
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			['name', 'trim'],
			['name', 'required'],
			['name', 'string', 'min' => 3, 'max' => 100],
			
			['status', 'in', 'range' => [Category::STATUS_DISABLED, Category::STATUS_ACTIVE]],
			
			['slug', 'required'],
			['slug', 'unique', 'targetClass' => '\common\models\Category', 'message' => 'Такой Slug для категории уже существует.', 'filter' => function ($query) {
                if ($this->id) {
					$query->andWhere(['not', ['id'=>$this->id]]);
                }
            }],
			
			[['id', 'parent'], 'safe'],
        ];
    }
	
    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function createNode()
    {
        if (!$this->validate()) {
            return null;
        }
		
		if (!$this->parent) {
			$roots = Category::find()->roots()->all();
			$this->parent = $roots[0];
		} else {
			$this->parent = Category::findOne(['id' => $this->parent]);
		}
        
		$node = new Category([
			'name' => $this->name,
			'slug' => $this->slug,
			'status' => $this->status,
		]);
		$node->appendTo($this->parent);
        
        return $node ? $node : null;
    }
	
	public function updateCategory()
	{
		if (!$this->validate()) {
            return null;
        }
		
		$node = Category::findOne(['id' => $this->id]);

		if (empty($this->slug)) {
			$this->slug = \yii\helpers\Inflector::slug($this->name, '');
		}
		
        $node->name = $this->name;
        $node->slug = $this->slug;
		$node->status = $this->status;
		
		return $node->save() ? $node : null;
	}
	
	public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
			'slug' => 'Slug',
			'parent' => 'Родитель',
			'status' => 'Статус',
        ];
    }
}
