<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'art',
            'name_ru',
            [
              'attribute' => 'category_id',
              'label' => 'Категория',
              'value' => 'category.name',
            ],
            [
              'attribute' => 'material_id',
              'label' => 'Материал',
              'value' => 'material.name',
            ],
            [
              'attribute' => 'size_id',
              'label' => 'Размер',
              'value' => 'size.name',
            ],
            [
				'attribute' => 'status',
				'content'=>function($data){
					return Html::tag('span', common\models\Product::getStatusList()[$data->status], ['class' => 'label label-' . ($data->status > 0 ? 'success' : 'danger')]);
				},
				'filter' => common\models\Product::getStatusList(),
                'format' => 'raw',
			],
            
            //'id',
            //'color',
            //'name_en',
            //'picture_id',
            //'barcode',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
