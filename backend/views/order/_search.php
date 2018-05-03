<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

use backend\models\Order;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search box-group" id="accordion">
    
    <div class="panel box box-success">
        <div class="box-header">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">Фильтры</a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse <?php if (count(Yii::$app->request->get('OrderSearch')) > 0) { ?>in<?php } ?>">
            <div class="box-body">
    
                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                ]); ?>

                <div class="row">
                   
                    <div class="col-md-2">
                        <?= $form->field($model, 'status')->checkboxList(Order::getStatusList()) ?>
                    </div>
                   
                    <div class="col-md-2">
                        <?= $form->field($model, 'payment_type')->checkboxList(Order::getPTList()) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($model, 'delivery_type')->checkboxList(Order::getDTList()) ?>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Дата поступления</label>

                            <div class="input-group">
                                <div class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="daterange1" name="OrderSearch[create_period]" value="<?= Yii::$app->request->get('OrderSearch')['create_period'] ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Дата сдачи</label>

                            <div class="input-group">
                                <div class="input-group-addon">
                                   <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="daterange2" name="OrderSearch[delivery_period]" value="<?= Yii::$app->request->get('OrderSearch')['delivery_period'] ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <?= $form->field($model, 'nonpayedonly')->checkbox() ?>
                        
                        <?= $form->field($model, 'payedonly')->checkbox() ?>
                    </div>
                    
                    <div class="col-md-2">
                        <?= $form->field($model, 'manager_id')->dropDownList($model->getManagersList(), ['prompt' => 'все']) ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                    <?= Html::submitButton('Выбрать', ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
                
            </div>
        </div>
    </div>

</div>

<br>

<?php $this->registerJs("
    $('#daterange1, #daterange2').daterangepicker({autoUpdateInput: false});
    
    $('#daterange1, #daterange2').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
"); ?>