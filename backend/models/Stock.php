<?php

    namespace backend\models;

    use Yii;
    use yii\base\Model;
    use yii\helpers\ArrayHelper;
    use backend\models\StockItem;
    
    /**
     *
     */
    class Stock extends Model
    {
        /**
         * Получить количество готовых позиций на складе
         * @param int $product_id id-товара
         * @param int $item_id id-позиции в заказе 
         */
        public static function getReadyProductQuantity($product_id, $item_id = null)
        {
            $q = (new \yii\db\Query())
                ->select('count(*) AS c')
                ->from(StockItem::tableName())
                ->where(['and', 'product_id = :id', 'status = 0'], [':id' => $product_id]);
            
            if (!is_null($item_id)) {
                $q->andFilterWhere(['and', 'order_item_id = ' . $item_id]);
            }
            
            $c = $q->one();
            
            return (int) $c['c'];
        }
    }