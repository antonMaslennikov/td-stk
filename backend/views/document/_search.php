<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Document;

/* @var $this yii\web\View */
/* @var $model backend\models\DocumentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<br>

<div class="document-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="col-sm-2">
         <?= $form->field($model, 'onlypayed')->checkbox() ?>
    </div>
    <div class="col-sm-2">
        <?= $form->field($model, 'manager')->dropDownList(Document::getManagerList(), ['prompt' => 'Менеджер...'])->label(false) ?>
    </div>
    <div class="col-sm-2">
        
        <?= $form->field($model, 'dateStart')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Дата С'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'autoclose'=>true,
            ]
        ])->label(false);
        ?>
    </div>     
    <div class="col-sm-2">
        
        <?= $form->field($model, 'dateEnd')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Дата До'],
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'autoclose'=>true,
            ]
        ])->label(false);
        ?>
        
    </div>
    <div class="col-sm-3 form-group">
        
         <?= $form->field($model, 'search')->textInput(['placeholder' => 'id, номер документа, инн'])->label(false) ?>
    </div>
    <div class="col-sm-1">
        <?= Html::submitButton('Выбрать', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

         <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
         <?= $form->field($model, 'direction')->hiddenInput()->label(false) ?>
   
    <?php ActiveForm::end(); ?>

</div>
