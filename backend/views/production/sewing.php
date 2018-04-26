<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пошив';
$this->params['breadcrumbs'][] = ['label' => 'Производство', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-sewing">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'formatter' => [
            'class'=>'yii\i18n\Formatter', 
            'dateFormat'=>'d MMM yyyy г.', 
            'locale'=>'ru'
        ],
        'layout'=>"{pager}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Товар',
                'attribute' => 'name_ru',
                'content' => function ($data) {
                    return Html::a($data['name_ru'], Url::to(['product/view', 'id' => $data['product_id']]), ['target' => '_blank']);
                }
            ],
            [
                'label' => 'Цвет',
                'attribute' => 'color',
            ],
            [
                'label' => 'Размер',
                'attribute' => 'size',
            ],
            [
                'label' => 'Количество (чистое на складе)',
                'attribute' => 'quantity',
                'content' => function($data){
                    
                    // если это позиция с печатью, то ищем на складе анналогичную позицию, но чистую и предлагаем заменить
                    if ($data['design_id'] > 0) 
                    {
                        $clearOnStock = Yii::$app->db
                            ->createCommand("select count(s.id) AS c 
                              from product__stock s, product p 
                              where 
                                    p.id = s.product_id
                                and s.order_item_id = 0
                                and p.category_id = :cat
                                and p.color_id = :color
                                and p.size_id = :size
                                and p.design_id = 0", [':cat' => $data['category_id'], ':color' => $data['color_id'], 'size' => $data['size_id']])
                            ->queryScalar();
                    }
                    
                    return $data['quantity'] - $data['quantity_from_stock'] . ' ' . ($clearOnStock > 0 ? '(' . min($clearOnStock, $data['quantity']) . ' шт. ' . Html::a('взять', ['takefromclear', 'id' => $data['id']]) . ')' : '');
                }
            ],
            [
                'label' => 'Состав',
                'content'=>function($data){
                    return '-';
                }
            ],
            [
                'label' => 'Расход',
                'content'=>function($data){
                    return '-';
                }
            ],
            [
                'label' => 'Дата сдачи',
                'format' => 'date', 
                'attribute' => 'delivery_date',
            ],
            [
                'label' => 'Заказ',
                'attribute' => 'order_id',
                'content' => function ($data) {
                    if ($data['order_id'] > 0) {
                        return Html::a($data['order_id'], Url::to(['order/view', 'id' => $data['order_id']]), ['target' => '_blank']);
                    }
                }
            ],
            [
                'label' => 'Менеджер',
                'attribute' => 'manager',
            ],
            [
                'label' => 'Готовность',
                'content' => function ($data) {
                    
                    if ($data['design_id'] > 0)
                        return Html::a('Сдать в печать', Url::to(['production/move2printing', 'id' => $data['id']]), ['class' => 'btn btn-xs btn-primary']);
                    else
                        return Html::a('Сдать на склад', Url::to(['production/move2reserv', 'id' => $data['id']]), ['class' => 'btn btn-xs btn-info']);
                }
            ],
            
        ],
    ]); ?>

</div>
