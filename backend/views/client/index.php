<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-client-index">

    <p>
        <?= Html::a('Добавить нового', ['add'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'email:email',
            'phone',
            'org',
            //'bank',
            //'bik',
            //'ks',
            //'rs',
            //'kpp',
            //'inn',
            //'dir',
            //'address',
            //'orgn',
            //'okpo',
            //'okato',
            //'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'', 
                'headerOptions' => ['width' => '30'],
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'delete' => function ($url,$model) {
                        if ($model->id == 1) {
                            return '';
                        } else {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id]);
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>
