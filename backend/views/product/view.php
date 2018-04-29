<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->name_ru;
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <div class="row">
        <div class="col-sm-3">
            <?= Html::beginForm(['product/tostock', 'id' => $model->id], 'post') ?>

                Внести на склад
                <div class="input-group">
                    <?= Html::textInput('q', '1', ['placeholder' => 'штук', 'class' => 'form-control']) ?>
                    <div class="input-group-btn">
                      <?= Html::submitButton('Внести', ['class' => 'btn btn-info']) ?>
                    </div>
                </div>

            <?= Html::endForm() ?>
        </div>
    </div>
    
    <br>
    
    <div class="row">
        <?php if (count($model->pictures)): ?>
        <div class="col-sm-2 product-previews">
            <?php foreach ($model->pictures AS $p): ?>
            <?= Html::a(Html::img($p->thumb), $p->path, ['data-fancybox'=> 'product']) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="col-sm-10">
            
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name_ru',
                    'name_en',
                    'slug',
                    'art',
                    'category_id',
                    'color_id',
                    'size_id',
                    'barcode',
                    'status',
                    'created_at:date',
                    'updated_at:date',
                ],
            ]) ?>
            
        </div>
    </div>

</div>
