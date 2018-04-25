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
        //'filterModel' => $searchModel,
        'layout'=>"{pager}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'product.name_ru',
                'format' => 'raw',
                'content' => function($data) {
                    return Html::a($data->product->name_ru, ['product/update', 'id' => $data->id], ['target' => '_blank']);
                }
            ],
            'order_item_id',
            /*
            [
                'attribute' => 'color.id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data->color->name;
                }
            ],
            [
                'attribute' => 'size.id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data->size->name;
                }
            ]
            'quantity'
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
