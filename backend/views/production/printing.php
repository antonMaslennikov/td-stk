<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Печать';
$this->params['breadcrumbs'][] = ['label' => 'Производство', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-printing">

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
                'format' => 'raw',
                'value'=>function($data) { 
                    return Html::img($data['picture'], [
                        'style' => 'width:85px;'
                    ]);
                },
            ],
            
            [
                'label' => 'Принт',
                'content'=>function($data){
                    return '-';
                }
            ],
            [
                'label' => 'Количество цветов',
                'content'=>function($data){
                    return '-';
                }
            ],
            [
                'label' => 'Носитель',
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
                'label' => 'Количество',
                'attribute' => 'quantity',
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
                    return Html::a('Сдать на склад', Url::to(['production/move2reserv', 'id' => $data['id']]), ['class' => 'btn btn-xs btn-info']);
                }
            ],
            
        ],
    ]); ?>

</div>
