<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use backend\models\Order;
use backend\models\ProductionItems;

class Production extends Model
{
    /**
     * датапровейдер для таблицы пошива
     * @param  [[Type]] $params [[Description]]
     */
    public static function sewingSearch($params)
    {
        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM production__items WHERE status=:status', [':status' => ProductionItems::STATUS_ACCEPTED])->queryScalar();

        $provider = new SqlDataProvider([
            'sql' => 'SELECT 
                        pi.id,
                        pi.quantity, 
                        pi.quantity_from_stock,
                        o.id AS order_id, 
                        o.delivery_date, 
                        u.username AS manager, 
                        p.id AS product_id,
                        p.name_ru, 
                        p.category_id,
                        p.color_id,
                        p.size_id,
                        p.design_id,
                        p.picture,
                        s.`name` AS size, 
                        c.`name` AS color,
                        cat.`name` AS category
                      FROM 
                        {{production__items}} pi, 
                        {{order__item}} oi, 
                        {{product}} p
                            LEFT JOIN {{sizes}} s ON s.`id` = p.`size_id`
                            LEFT JOIN {{color}} c ON c.`id` = p.`color_id`
                            LEFT JOIN {{category}} cat ON cat.`id` = p.`category_id`,
                        {{order}} o 
                            LEFT JOIN {{user}} u ON o.`manager_id` = u.`id`
                      WHERE 
                            pi.status=:status 
                        AND pi.item_id = oi.id 
                        AND oi.order_id = o.id
                        AND pi.`product_id` = p.id',
            'params' => [':status' => ProductionItems::STATUS_ACCEPTED],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'title',
                    'view_count',
                    'created_at',
                ],
            ],
        ]);

        // возвращает массив данных
        //$models = $provider->getModels();
        
        return $provider;
    }
    
    /**
     * Данные для странице пошива сгруппированные по носителю
     */
    public static function sewingGrouped()
    {
        $products = (new \yii\db\Query())
                    ->select(['category' => 'c.name', 'color' => 'co.name', 'size' => 's.name', 'p.sex', 'quantity' => 'sum(pi.quantity - pi.quantity_from_stock)'])
                    ->from(['pi' => 'production__items', 'p' => 'product', 'c' => 'category', 'co' => 'color', 's' => 'sizes'])
                    ->where('pi.product_id = p.id AND pi.status = :status AND c.id = p.category_id AND co.id = p.color_id AND s.id = p.size_id')
                    ->addParams([':status' => ProductionItems::STATUS_ACCEPTED])
                    ->groupBy('concat(p.`category_id`, p.`sex`, p.`color_id`, p.`size_id`)')
                    ->all();
        
        //printr($products);
        
        return (array) $products;
    }
    
    /**
     * датапровейдер для таблицы печати
     * @param  [[Type]] $params [[Description]]
     */
    public static function printingSearch($params)
    {
        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM production__items WHERE status=:status', [':status' => ProductionItems::STATUS_PRINTING])->queryScalar();

        $provider = new SqlDataProvider([
            'sql' => 'SELECT 
                        pi.id,
                        pi.quantity, 
                        o.id AS order_id, 
                        o.delivery_date, 
                        u.username AS manager, 
                        p.id AS product_id,
                        p.name_ru, 
                        p.design_id,
                        p.picture,
                        s.`name` AS size, 
                        c.`name` AS color
                      FROM 
                        {{production__items}} pi, 
                        {{order__item}} oi, 
                        {{product}} p
                            LEFT JOIN {{sizes}} s ON s.`id` = p.`size_id`
                            LEFT JOIN {{color}} c ON c.`id` = p.`color_id`,
                        {{order}} o 
                            LEFT JOIN {{user}} u ON o.`manager_id` = u.`id`
                      WHERE 
                            pi.status=:status 
                        AND pi.item_id = oi.id 
                        AND oi.order_id = o.id
                        AND pi.`product_id` = p.id',
            'params' => [':status' => ProductionItems::STATUS_PRINTING],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => [
                    'title',
                    'view_count',
                    'created_at',
                ],
            ],
        ]);

        // возвращает массив данных
        //$models = $provider->getModels();
        
        return $provider;
    }
}