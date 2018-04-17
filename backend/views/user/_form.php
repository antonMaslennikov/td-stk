<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\RolesHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'username')->textInput() ?>
	
	<?= $form->field($model, 'fio')->textInput() ?>
	
	<?= $form->field($model, 'email')->textInput() ?>
	
	<?php if (\Yii::$app->user->can(RolesHelper::ADMIN)): ?>
        <?= $form->field($model, 'status')->dropDownList(\common\models\User::getStatuses()) ?>
        <?= $form->field($model, 'role')->dropDownList(\common\components\RolesHelper::getList()) ?>
    <?php endif; ?>
	
	<?= $form->field($model, 'avatar')->fileInput() ?>

	<?= $form->field($model, 'password')->passwordInput() ?>
	
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
