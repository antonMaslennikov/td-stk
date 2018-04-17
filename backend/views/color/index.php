<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ColorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Цвета';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="color-index">

    <p>
        <?= Html::a('Добавить новый цвет', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
				'attribute' => 'hex',
				'format' => 'raw',
				'value' => function($data){
					return Html::tag('span', "&nbsp;", ['class' => 'color-block', 'style' => 'background-color:#' . $data->hex  . '']) . $data->hex;
				}
			],
			[
				'attribute' => 'group',
				'content'=>function($data){
					return common\models\Color::$groups[$data->group]['title'];
				},
				'filter' => common\models\Color::getGroups(),
			],

            [
				'class' => 'yii\grid\ActionColumn',
				'header'=>'Действия', 
				'headerOptions' => ['width' => '80'],
				'template' => '{update} {delete}',
			],
        ],
    ]); ?>
</div>
