<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    use kartik\date\DatePicker;

    use backend\models\Order;
    use backend\components\Geo;

    $deliveryForm = new \backend\models\OrderDeliveryForm;

    $deliveryForm->delivery_cost = $model->delivery_cost;
    $deliveryForm->delivery_type = $model->delivery_type;
    $deliveryForm->delivery_date = $model->delivery_date;

    $deliveryForm->country = $model->address->country;
    $deliveryForm->city = $model->address->city;
    $deliveryForm->address = $model->address->address;
?>

<?php $form = ActiveForm::begin(['action' => Url::to(['order/savedelivery']), 'id' => 'deliveryForm']); ?>

    <table class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <tr>
           <th colspan="2">Доставка</th>
        </tr>
        <tr>
            <td>Тип доставки</td>
            <td>
                <span><?= $model->delivery_type_rus ?></span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'delivery_type')->dropDownList(Order::getDTList())->label(false) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>Страна</td>
            <td>
                <span><?= $model->address->country_name ?></span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'country')->dropDownList(Geo::getCountrys())->label(false) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>Город</td>
            <td>
                <span><?= $model->address->city_name ?></span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'city')->dropDownList(Geo::getCitys())->label(false) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>Адрес</td>
            <td>
                <span><?= $model->address->address ?></span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'address')->textInput()->label(false) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>Стоимость доставки</td>
            <td>
                <span><?= $model->delivery_cost ?> руб.</span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'delivery_cost')->textInput()->label(false) ?>
                </div>
            </td>
        </tr>
        <tr style="background-color:#d5ecf1">
            <td>Дата доставки</td>
            <td>
                <span><?= $model->delivery_date == '0000-00-00' ? 'Не задана' : $model->delivery_date ?></span>
                <div class="hh hidden">
                    <?= $form->field($deliveryForm, 'delivery_date')->widget(DatePicker::classname(), [
                            'removeButton' => false,
                            'options' => ['placeholder' => 'Дата доставки', 'value' => $model->delivery_date == '0000-00-00' ? '' : $model->delivery_date],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true,
                            ]
                        ])->label(false);
                    ?>
                </div>
            </td>
        </tr>
        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
        <tfoot>
        <tr>
            <th style="border-right: none">
               <?= Html::submitButton('сохранить', ['class' => 'btn btn-block-sm btn-success', 'style' => 'display:none']) ?>
            </th>
            <th style="text-align: right;border-left: none">
                <button class="btn btn-block-sm btn-default toggleForm" data-form_id="deliveryForm" style="display: none">отменить</button>
                <button class="btn btn-block-sm btn-warning toggleForm" data-form_id="deliveryForm"><i class="fa fa-fw fa-pencil"></i>изменить</button>
            </th>
        </tr>    
        </tfoot>
        <?php endif; ?>
    </table>

    <?= $form->field($deliveryForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>

<?php ActiveForm::end(); ?> 