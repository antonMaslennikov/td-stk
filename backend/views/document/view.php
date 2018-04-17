<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use backend\models\Document;

/* @var $this yii\web\View */
/* @var $model backend\models\Document */

$this->title = Document::getTypes()[$model->type] . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-view">

    <div class="row">
        <div class="col-sm-6">
            
            <?= Html::a('Скачать XLS', ['download', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
            
        </div>
        <div class="col-sm-6">
            <div class="pull-right">
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот документ?',
                    'method' => 'post',
                ],
            ]) ?>
            </div>
        </div>
    </div>

    <br>
   
    <?= DetailView::widget([
        'model' => $model,
        'formatter' => [
            'class'=>'yii\i18n\Formatter', 
            'dateFormat'=>'d MMM yyyy г.', 
            'currencyCode' => 'RUR',
            'locale'=>'ru'
        ],
        'attributes' => [
            'id',
            'name',
            'number',
            'date:date',
            [
                'attribute' => 'order_id',
                'format' => 'raw',
                'value' => function($data){
                    return $data->order_id ? Html::a($data->order_id, Url::to(['order/view', 'id' => $data->order_id])) : "&mdash;";
                }
            ],
            'sum:currency',
            'sum_payed:currency',
            [
                'attribute' => 'payed',
                'format' => 'raw',
                'value' => function($data){
                    return $data->payed ? '<span class="label label-success">оплачен</span>' : '<span class="label label-danger">не оплачен</span>';
                }
            ]
            ,
            //'payment_type',
        ],
    ]) ?>

    <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <tr>
            <th>#</th>
            <th>Наименование</th>
            <th>Цена, р.</th>
            <th>Количество</th>
            <th>Сумма, р.</th>
        </tr>
        <?php if (count($model->positions) > 0): ?>
            <?php foreach($model->positions AS $k => $p): ?>
            <tr>
                <td><?= $k + 1 ?></td>
                <td><?= $p->name ?></td>
                <td><?= $p->price ?></td>
                <td><?= $p->quantity ?></td>
                <td><?= $p->price * $p->quantity ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="4">Итого</th>
                <th><?= $model->sum ?> р.</th>
            </tr>
        <?php else: ?>
        <tr>
            <td colspan="10">Позиции отсутствуют</td>
        </tr>
        <?php endif; ?>
    </table>

</div>
