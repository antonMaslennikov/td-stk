<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StockItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Склад готовой продукции';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="stock-item-index">
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{pager}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'name_ru',
                'format' => 'raw',
                'content' => function($data) {
                    return Html::a($data['name_ru'], ['product/view', 'id' => $data['product_id']]);
                }
            ],
            [
                'attribute' => 'category_id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data['category'];
                }
            ],
            [
                'attribute' => 'color_id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data['color'];
                }
            ],
            [
                'attribute' => 'size_id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data['size'];
                }
            ],
            [
                'attribute' => 'quantity',
                'format' => 'raw',
                'content' => function($data) {
                    return $data['quantity'];
                }
            ],
            //'order_item_id',
            /*
            
            [
				'class' => 'yii\grid\ActionColumn',
				'header'=>'Действия', 
				'headerOptions' => ['width' => '80'],
				'template' => '{view}',
			],
            */
        ],
    ]); ?>
</div>
