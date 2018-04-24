<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    use backend\models\Order;

    $paymentForm = new \backend\models\OrderPaymentForm;
    $payForm = new \backend\models\OrderPayForm;
?>

<?php $form = ActiveForm::begin(['action' => Url::to(['order/savepayment']), 'id' => 'paymentForm']); ?>

    <table id="example2" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example2_info">
        <tr>
           <th colspan="2">Оплата</th>
        </tr>
        <tr>
            <td>Тип оплаты</td>
            <td>
                <span><?= $model->payment_type_rus ?></span>
                <div class="hh hidden">
                    <?= $form->field($paymentForm, 'payment_type')->dropDownList(Order::getPTList())->label(false) ?>
                </div>

                <?php if ($model->payment_confirm > 0): ?>
                    <span class="label label-success margin">
                        Оплачен полностью
                        <?php if ($model->alreadyPayed > $model->sum + $model->delivery_cost): ?>
                        . переплата <?= $model->alreadyPayed - $model->sum - $model->delivery_cost ?> р.
                        <?php endif; ?>
                    </span>
                <?php else: ?>
                    <?php if ($model->alreadyPayed > 0): ?>
                        <span class="label label-warning margin">Оплачен на <?= $model->alreadyPayed ?> руб.</span>
                    <?php else: ?>
                        <span class="label label-danger margin">Не оплачен</span>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>

        <?php if ($model->payment_partical > 0): ?>
        <tr>
            <td>Оплачено купоном</td>
            <td><?= $model->payment_partical ?> руб.</td>
        </tr>
        <?php endif; ?>

        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
        <tfoot>
            <tr>
                <th style="border-right: none">
                    <button class="btn btn-block-sm btn-success" name="save" style="display: none">сохранить</button>
                </th>
                <th style="text-align: right;border-left: none">
                    <button class="btn btn-block-sm btn-default toggleForm" data-form_id="paymentForm" style="display: none">отменить</button>
                    <button class="btn btn-block-sm btn-warning toggleForm" data-form_id="paymentForm"><i class="fa fa-fw fa-pencil"></i>изменить</button>
                </th>   
            </tr>    
        </tfoot>
        <?php endif ?>
    </table>

    <?= $form->field($paymentForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>

<?php ActiveForm::end(); ?> 


<table id="example3" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example3_info">
<tr>
   <th colspan="2">
       Счета на оплату
       <?= Html::a('<button class="btn btn-xs btn-success"><i class="fa fa-fw fa-plus"></i></button>', '#addBillModal', ['data-toggle' => 'modal', 'class' => 'pull-right']) ?>
   </th>
</tr>
<tr>
    <td colspan="2">
        <?php if (count($model->clientBills) > 0): ?>

            <?php foreach ($model->clientBills AS $b): ?>

                <?= Html::a($b->number, Url::to(['document/view', 'id' => $b->id])) ?>, 

            <?php endforeach; ?>

        <?php else: ?>
            Отсутствуют
        <?php endif; ?>
    </td>
</tr>
</table>


<?php if ($model->status != Order::STATUS_CANCELED && $model->payment_confirm <= 0 && $model->sum + $model->delivery_cost > 0): ?>

    <?php $form = ActiveForm::begin(['action' => Url::to(['order/setpayment'])]); ?>

        <?= $form->field($payForm, 'sum')->textInput(['value' => $model->sum + $model->delivery_cost - $model->alreadyPayed, 'placeholder' => 'укажите сумму', 'class' => 'form-control']) ?>

        <?= $form->field($payForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>

        <?= $form->field($payForm, 'payment_type')->hiddenInput(['value' => $model->payment_type])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Оплатить', ['class' => 'btn btn-info']) ?>
        </div>

    <?php ActiveForm::end(); ?>

<?php endif; ?>