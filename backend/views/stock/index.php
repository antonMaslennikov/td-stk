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
                'attribute' => 'name_ru',
                'format' => 'raw',
                'content' => function($data) {
                    return Html::a($data->name_ru, ['product/update', 'id' => $data->id], ['target' => '_blank']);
                }
            ],
            [
                'attribute' => 'color_id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data->color->name;
                }
            ],
            [
                'attribute' => 'size_id',
                'format' => 'raw',
                'content' => function($data) {
                    return $data->size->name;
                }
            ],
            'quantity',
            [
				'class' => 'yii\grid\ActionColumn',
				'header'=>'Действия', 
				'headerOptions' => ['width' => '80'],
				'template' => '{view}',
			],
        ],
    ]); ?>
</div>
