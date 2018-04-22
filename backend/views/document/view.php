<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use backend\models\Document;
use backend\components\CreateDocumentWidget;

/* @var $this yii\web\View */
/* @var $model backend\models\Document */

$this->title = Document::getTypes()[$model->type] . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index', 'DocumentSearch[type]' => $model->type, 'DocumentSearch[direction]' => $model->direction]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-view">

    <div class="row">
        <div class="col-sm-4">
            <?= Html::a('Скачать XLS', ['download', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        </div>
        
        <div class="col-sm-4">
            <?php if ($model->type == Document::TYPE_BILL): ?>
                
                <?php $childrens = $model->childrens; ?>
                
                <?php if ($childrens[Document::TYPE_AKT]): ?>
                    <?= Html::a('Открыть акт', Url::to(['document/view', 'id' => $childrens[Document::TYPE_AKT]->id]), ['class' => 'btn btn-default']) ?>
                <?php else: ?>
                    <?= Html::a('Создать акт', '#addAktModal', ['data-toggle' => 'modal', 'class' => 'btn btn-default']) ?>
                <?php endif; ?>
                <?php if ($childrens[Document::TYPE_NAKL]): ?>
                    <?= Html::a('Открыть накладную', Url::to(['document/view', 'id' => $childrens[Document::TYPE_NAKL]->id]), ['class' => 'btn btn-default']) ?>
                <?php else: ?>
                    <?= Html::a('Создать накладную', '#addNaklModal', ['data-toggle' => 'modal', 'class' => 'btn btn-default']) ?>
                <?php endif; ?>
                
                <?= CreateDocumentWidget::widget(['type' => Document::TYPE_AKT, 'parent' => $model]) ?>
            
                <?= CreateDocumentWidget::widget(['type' => Document::TYPE_NAKL, 'parent' => $model]) ?>
            
            <?php elseif (($model->type == Document::TYPE_AKT || $model->type == Document::TYPE_NAKL) && $model->parent_id): ?>
                           
                <?= Html::a('Открыть счёт', Url::to(['document/view', 'id' => $model->parent_id]), ['class' => 'btn btn-default']) ?>
                           
            <?php endif; ?>
            
        </div>
        
        <div class="col-sm-4">
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
            [
                'attribute' => 'client_id',
                'format' => 'raw',
                'value' => function($data){
                    return $data->client_id ? Html::a($data->client->name, Url::to(['client/view', 'id' => $data->client_id])) : "&mdash;";
                }
            ],
            'sum:currency',
            'sum_payed:currency',
            [
                'attribute' => 'payed',
                'format' => 'raw',
                'value' => function($data){
                    return $data->payed ? '<span class="label label-success">оплачен</span>' : ($data->sum_payed > 0 ? '<span class="label label-warning">оплачен частично</span>' : '<span class="label label-danger">не оплачен</span>');
                }
            ]
            ,
            [
                'attribute' => 'payment_type',
                'value' => function($data){
                    return Document::getPaymentTypes()[$data->payment_type];
                }
            ],
        ],
    ]) ?>
    
    <?php if ($model->type == Document::TYPE_BILL): ?>
        <div class="row">
            <div class="col-sm-4">
                <h4>Оплатить счёт</h4>
                
                <?php if ($model->sum - $model->sum_payed > 0): ?>
                    <?= Html::beginForm(['document/pay'], 'post') ?>
                    <div class="input-group">
                        <input type="text" class="form-control" name="sum" placeholder="укажите сумму" value="<?= $model->sum - $model->sum_payed ?>">
                        <div class="input-group-btn">
                          <button type="submit" class="btn btn-info">Оплатить счёт</button>
                        </div>
                        <?= Html::hiddenInput('id', $model->id) ?>
                    </div>
                    <?= Html::endForm() ?>
                <?php else: ?>
                    Счёт оплачен полностью
                <?php endif; ?>
            </div>
        </div>
        <br>
    <?php endif; ?>

    <div class="row">
        <div class="col-sm-12">
            <h4>Позиции</h4>
        </div>
    </div>
   
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
