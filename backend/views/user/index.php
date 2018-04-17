<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    'emptyCell' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
			'fio',
            'email:email',
            [
				'attribute' => 'status',
				'content'=>function($data){
					return common\models\User::getStatuses()[$data->status];
				},
				'filter' => common\models\User::getStatuses(),
			],
			[
				'attribute' => 'role',
				'content'=>function($data){
					return common\components\RolesHelper::getList()[$data->role];
				},
				'filter' => common\components\RolesHelper::getList(),
			],
            [
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:Y-m-d h:i:s'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
