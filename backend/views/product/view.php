<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Product */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <p>
        <?= Html::a('Внести на склад 1 шт', ['tostock', 'id' => $model->id, 'q' => 1], ['class' => 'btn btn-info btn-xs']) ?>
        <?= Html::a('Внести на склад 3 шт', ['tostock', 'id' => $model->id, 'q' => 3], ['class' => 'btn btn-info btn-xs']) ?>
        <?= Html::a('Внести на склад 5 шт', ['tostock', 'id' => $model->id, 'q' => 5], ['class' => 'btn btn-info btn-xs']) ?>
        <?= Html::a('Внести на склад 10 шт', ['tostock', 'id' => $model->id, 'q' => 10], ['class' => 'btn btn-info btn-xs']) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name_ru',
            'name_en',
            'slug',
            'art',
            'category_id',
            'material_id',
            'color_id',
            'size_id',
            'picture',
            'barcode',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
