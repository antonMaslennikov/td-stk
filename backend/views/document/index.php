<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use backend\models\Document;
use common\models\Order;
use backend\components\CreateDocumentWidget;
use backend\components\ClientSearchWidget;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if (Yii::$app->request->get('DocumentSearch')['type'] == Document::TYPE_BILL)
    $this->title = 'Счета';
elseif (Yii::$app->request->get('DocumentSearch')['type'] == Document::TYPE_AKT)
    $this->title = 'Акты';
elseif (Yii::$app->request->get('DocumentSearch')['type'] == Document::TYPE_NAKL)
    $this->title = 'Накладные';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-index">

    <?php if (Yii::$app->request->get('DocumentSearch')['type'] == Document::TYPE_BILL): ?>
    <p>
        <?= Html::a('<button class="btn btn-success"><i class="fa fa-fw fa-plus"></i> Добавить новый</button>', '#searchClientModal', ['data-toggle' => 'modal']) ?>
        <?= \backend\components\ClientSearchWidget::widget(['goBack' => Url::current([])]); ?>
       
        <?= CreateDocumentWidget::widget(['type' => Yii::$app->request->get('DocumentSearch')['type']]) ?>
        
        <?
            if (Yii::$app->request->get('SearchClientForm')['client_id'])
            {
                $this->registerJs('$(\'#addBillModal\').modal(\'show\');');
            }
        ?>
    </p>
    
    <ul class="nav nav-tabs">
        <li <?php if (Yii::$app->request->get('DocumentSearch')['payment_type'] == Document::PT_CASH): ?>class="active"<?php endif; ?>>
            <?= Html::a('Наличные', Url::to(['index', 'DocumentSearch[type]' => Yii::$app->request->get('DocumentSearch')['type'], 'DocumentSearch[direction]' => Yii::$app->request->get('DocumentSearch')['direction'], 'DocumentSearch[payment_type]' => Document::PT_CASH])) ?>
        </li>
        <li <?php if (Yii::$app->request->get('DocumentSearch')['payment_type'] == Document::PT_CARD): ?>class="active"<?php endif; ?>>
            <?= Html::a('Наличные на карту', Url::to(['index', 'DocumentSearch[type]' => Yii::$app->request->get('DocumentSearch')['type'], 'DocumentSearch[direction]' => Yii::$app->request->get('DocumentSearch')['direction'], 'DocumentSearch[payment_type]' => Document::PT_CARD])) ?>
        </li>
    </ul>
    
    <?php endif; ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'formatter' => [
            'class'=>'yii\i18n\Formatter', 
            'dateFormat'=>'d MMM yyyy г.', 
            'currencyCode' => 'RUR',
            'locale'=>'ru'
        ],
        'layout'=>"{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a($data->name, Url::to(['view', 'id' => $data->id]));
                }
            ],
            'number',
            'quantity',
            'sum:currency',
            'sum_payed:currency',
            [
                'attribute' => 'order_id',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a($data->order_id, Url::to(['order/view', 'id' => $data->order_id])) . '<span class="label pull-right label-default">' . Order::getStatusList()[$data->order_status] . '</span>';
                }
            ],
            'manager',
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'', 
                'headerOptions' => ['width' => '30'],
                'template' => '{download}&nbsp;&nbsp;{view} {update} {delete}',
                'buttons' => [
                    'download' => function ($url,$model) {
                        return Html::a(
                        '<span class="glyphicon glyphicon-download-alt"></span>', 
                        $url);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
