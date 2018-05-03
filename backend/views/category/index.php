<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategoryQuery */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <p>
        <?= Html::a('Новая категория', ['create'], ['class' => 'btn btn-success']) ?>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<!--?= Html::a('Create Root', ['createroot'], ['class' => 'btn btn-info']) ?-->
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
				'attribute' => 'name',
				'format' => 'raw',
				'value' => function($data){
					return (str_repeat('–', $data->depth)) . ' ' . Html::a($data->name, Url::to(['product/index', 'ProductSearch[category_id]' => $data->id]));
				}
			],
			'slug',
			[
				'attribute' => 'status',
				'content'=>function($data){
					return Html::tag('span', common\models\Category::getStatusList()[$data->status], ['class' => 'label label-' . ($data->status > 0 ? 'success' : 'danger')]);
				},
				'filter' => common\models\Category::getStatusList(),
			],
			//'lft',
            //'rgt',
            //'depth',

            [
				'class' => 'yii\grid\ActionColumn',
				'header'=>'Действия', 
				'headerOptions' => ['width' => '80'],
				'template' => '{update} {delete}',
			],
        ],
    ]); ?>
</div>
