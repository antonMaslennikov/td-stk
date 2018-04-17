<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use backend\models\Document;
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
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            'sum',
            //'date',
            //'order_id',
            //'sum_payed',
            //'payed',
            //'payment_type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
