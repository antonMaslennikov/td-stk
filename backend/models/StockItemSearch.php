<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class StockItemSearch extends StockItem
{
    public $name;
    public $category;
    public $material;
    public $size;
    public $quantity;
    
    public $onstock = false;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'order_item_id'], 'integer'],
            [['name'], 'safe'],
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
        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM product__stock WHERE order_item_id=0', [])->queryScalar();

        $provider = new SqlDataProvider([
            'sql' => 'SELECT 
                        p.id AS product_id,
                        p.name_ru, 
                        p.category_id,
                        p.color_id,
                        p.size_id,
                        p.design_id,
                        s.name AS size, 
                        c.name AS color,
                        cat.name AS category,
                        COUNT(ps.`id`) AS quantity
                      FROM 
                        {{product__stock}} ps, 
                        {{product}} p
                            LEFT JOIN {{category}} cat ON cat.`id` = p.`category_id`
                            LEFT JOIN {{sizes}} s ON s.`id` = p.`size_id`
                            LEFT JOIN {{color}} c ON c.`id` = p.`color_id`
                      WHERE 
                            1
                        AND ps.`product_id` = p.id
                      GROUP BY p.id',
            'params' => [],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => [
                    'title',
                    'view_count',
                    'created_at',
                ],
            ],
        ]);

        return $provider;
    }
}
