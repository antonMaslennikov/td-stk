<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

use \backend\models\Document;

/* @var $this yii\web\View */
/* @var $model backend\models\Document */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'payment_type')->dropDownList(Document::getPaymentTypes()) ?>
   
    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
        'removeButton' => false,
        'options' => ['placeholder' => 'Дата', 'value' => date('Y-m-d')],
        'pluginOptions' => [
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
        ]
    ]);
    ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
