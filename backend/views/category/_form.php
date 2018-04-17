<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\Category;

/* @var $this yii\web\View */
/* @var $model common\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'slug')->textInput() ?>
	
	<?= $form->field($model, 'parent')->dropDownList(Category::getAllTree()) ?>
	
	<?= $form->field($model, 'status')->dropDownList(Category::getStatusList()) ?>
	
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
