<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use backend\models\Order;
use backend\components\ClientSearchInputWidget;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->widget(ClientSearchInputWidget::className(), ['client' => $model->client]) ?>

    <?= $form->field($model, 'address_id')->dropDownList($model->getAddressList()) ?>
    
    <div id="hidden-address-block" style="<?= !$model->client || count($model->addressList) == 1 ? '' : 'display:none' ?>">
    <?= $form->field($model, 'address')->textInput() ?>
    </div>
    
    <?= $form->field($model, 'payment_type')->dropDownList(Order::getPTList(), ['prompt' => 'Выберите из списка',]) ?>

    <?= $form->field($model, 'delivery_type')->dropDownList(Order::getDTList(), ['prompt' => 'Выберите из списка',]) ?>

    <?= $form->field($model, 'delivery_cost')->textInput() ?>
    

    <div class="form-group">
        <?= Html::submitButton('Создать заказ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    
    <?= \backend\components\ClientSearchWidget::widget(['goBack' => Url::current([])]); ?>

    <?=
        $this->registerJs('
        $("select[name=OrderForm\\\[address_id\\\]]").change(function(){
           
            if ($(this).val() == -1) {
                $("#hidden-address-block").show();
            } else {
                $("#hidden-address-block").hide();
            }
            
        });
        ');
    ?>

</div>
