<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    public $category;
    public $material;
    public $size;
    public $print = true;
    
    public $onstock = false;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'color_id'], 'integer'],
            [['name_ru', 'name_en', 'slug', 'art', 'barcode', 'status', 'created_at', 'updated_at', 'category_id', 'material', 'size', 'picture', 'print'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find()
                    ->with('color')
                    ->with('size');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->joinWith('category');        
        $query->joinWith('material');
        $query->joinWith('size');
        
        if ($this->onstock) {
            $query->andWhere("`quantity` > '0'");
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'color_id' => $this->color_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name_ru', $this->name_ru])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'art', $this->art])
            ->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'product.status', $this->status])
            ->andFilterWhere(['=', 'product.category_id', $this->category_id]);

        if ($this->print === 'clear') {
            $query->andFilterWhere(['=', 'design_id', '0']);
        } else {
            $query->andFilterWhere(['>', 'design_id', '0']);
        }

        return $dataProvider;
    }
}
