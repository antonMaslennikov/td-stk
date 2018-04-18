<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderClientSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-client-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'org') ?>

    <?php // echo $form->field($model, 'bank') ?>

    <?php // echo $form->field($model, 'bik') ?>

    <?php // echo $form->field($model, 'ks') ?>

    <?php // echo $form->field($model, 'rs') ?>

    <?php // echo $form->field($model, 'kpp') ?>

    <?php // echo $form->field($model, 'inn') ?>

    <?php // echo $form->field($model, 'dir') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'orgn') ?>

    <?php // echo $form->field($model, 'okpo') ?>

    <?php // echo $form->field($model, 'okato') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
