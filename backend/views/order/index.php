<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use common\models\Order;
use backend\models\Order AS AdminOrder;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-index">

    <?php \Yii::$app->formatter->locale = 'ru-RU'; ?>
   
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
   
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
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'label' => 'Номер',
                'format' => 'raw',
                'headerOptions' => ['width' => '30'],
                'value' => function($data){
                    return Html::a(
                        $data->id,
                        Url::to(['order/view', 'id' => $data->id]),
                        [
                            'title' => 'Открыть',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'status',
                'content' => function($data){
                    return Order::getStatusList()[$data->status];
                },
                'filter' => Order::getStatusList()
            ],
            [
                'attribute' => 'client_id',
                'content' => function($data){
                    return $data->client->name;
                },
                'filter' => false
            ],
            'client.phone',
            [
                'attribute' => 'payment_type',
                'content' => function($data){
                    return Order::$paymentTypes[$data->payment_type]['name'];
                },
                'filter' => AdminOrder::getPTList()
            ],
            [
                'attribute' => 'delivery_type',
                'content' => function($data){
                    return Order::$deliveryTypes[$data->delivery_type]['name'];
                },
                'filter' => AdminOrder::getDTList()
            ],
            'sum:currency',
            'delivery_cost:currency',
            [
                'attribute' => 'created_at',
                'filter' => false,
                'format' => 'date',
            ],
            [
                'attribute' => 'delivery_date',
                'filter' => false,
                'content' => function($data){
                    return $data->delivery_date != '0000-00-00' ? \Yii::$app->formatter->asDate($data->delivery_date) : '';
                }   
            ],
            [
                'attribute' => 'payment_confirm',
                'content'=>function($data){
					return Html::tag('span', $data->payment_confirm > 0 ? 'оплачен' : 'не оплачен', ['class' => 'label label-' . ($data->payment_confirm > 0 ? 'success' : 'danger')]);
				},
            ],
            [
                'attribute' => 'manager',
                'filter' => false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'', 
                'headerOptions' => ['width' => '30'],
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                        '<span class="btn btn-info btn-xs"><i class="fa fa-fw fa-eye"></i></span>', 
                        $url);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
