<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    use yii\web\JsExpression;
?>

<div id="searchClientModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Заголовок модального окна -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Найти клиента или создать нового</h4>
      </div>
      <!-- Основное содержимое модального окна -->
      <div class="modal-body">
        
        <?php $form = ActiveForm::begin(['action' => $goBack, 'method' => 'get']); ?>
        
            <div class="form-group">
            <label>Клиент</label>
            <?= \yii\jui\AutoComplete::widget([
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Начните набирать имя, почту или телефон клиента',
                    ],
                    'clientOptions' => [
                        'source' => Url::to(['client/autocomplite']),
                        'select' =>new JsExpression("function(event, ui) {
                            $('#hidden_client_id_input').val(ui.item.id);
                        }"),
                    ],
                ]) 
            ?>
            
            <?= $form->field($search, 'client_id')->hiddenInput(['id' => 'hidden_client_id_input'])->label(false) ?>
            
            </div>
            
            <div class="form-group">
                <?= Html::submitButton('Выбрать', ['class' => 'btn btn-info']) ?>
                
                <?= Html::button('... или добавьте нового', ['class' => 'btn btn-warning pull-right', 'onclick' => '$(\'#createNewClientForm\').toggle()']) ?>
            </div>
        
        <?php ActiveForm::end(); ?>
            
        <?php $form = ActiveForm::begin([
            'action' => Url::to(['client/create']),
            'validationUrl' => Url::to(['client/validate']),
            'id' => 'createNewClientForm'
        ]); ?>

            <?= $form->field($model, 'name')->textInput() ?>

            <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput() ?>

            <?= $form->field($model, 'phone', ['enableAjaxValidation' => true])->textInput() ?>

            <?= Html::hiddenInput('goBack', $goBack) ?>
           
            <div class="form-group">
                <?= Html::submitButton('Добавить клиента', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        
        <?php $this->registerCss('#createNewClientForm {display:none}') ?>  
              
      </div>
      <!-- Футер модального окна -->
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>