<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <ul class="nav nav-tabs">
        <li <?php if (Yii::$app->request->get('ProductSearch')['print'] == 1 || !Yii::$app->request->get('ProductSearch')['print']): ?>class="active"<?php endif; ?>>
            <?= Html::a('С печатью', Url::to(['index'])) ?>
        </li>
        <li <?php if (Yii::$app->request->get('ProductSearch')['print'] == 'clear'): ?>class="active"<?php endif; ?>>
            <?= Html::a('Чистые', Url::to(['index', 'ProductSearch[print]' => 'clear'])) ?>
        </li>
    </ul>
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout'=>"{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'format' => 'raw',
                'value'=>function($data) { 
                    return Html::img($data->picture, [
                        'style' => 'width:85px;'
                    ]);
                },
            ],
            'art',
            'name_ru',
            [
              'attribute' => 'category_id',
              'label' => 'Категория',
              'value' => 'category.name',
            ],
            [
              'attribute' => 'color_id',
              'label' => 'Цвет',
              'value' => 'color.name',
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
