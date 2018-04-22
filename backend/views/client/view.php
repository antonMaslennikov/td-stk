<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrderClient */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-client-view">

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
        <?php if (!in_array($model->id, [1])): ?>
        
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этого клиента?',
                    'method' => 'post',
                ],
            ]) ?>
        
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'email:email',
            'phone',
            'org',
            'bank',
            'bik',
            'ks',
            'rs',
            'kpp',
            'inn',
            'dir',
            'address',
            'orgn',
            'okpo',
            'okato',
            'created_at:date',
        ],
    ]) ?>

</div>
