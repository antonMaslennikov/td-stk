<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Color;

use zgb7mtr\colorPicker\ColorPicker;

/* @var $this yii\web\View */
/* @var $model common\models\Color */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="color-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hex')->widget(ColorPicker::className()); ?>

    <?= $form->field($model, 'group')->dropDownList(Color::getGroups()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
