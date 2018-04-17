<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    use yii\web\JsExpression;
?>

<div id="addItemModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Заголовок модального окна -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Добавить товар в заказ</h4>
      </div>
      <!-- Основное содержимое модального окна -->
      <div class="modal-body">
        
        <?php $form = ActiveForm::begin(['action' => Url::to(['order/additem'])]); ?>
        
            <div class="form-group">
                <label>Товар</label>
                <?= \yii\jui\AutoComplete::widget([
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Начните набирать название товара или артикул',
                        ],
                        'clientOptions' => [
                            'source' => Url::to(['product/autocomplite']),
                            'select' =>new JsExpression("function(event, ui) {
                                $('#hidden_product_id_input').val(ui.item.id);
                            }"),
                        ],
                    ]) 
                ?>

                <?= $form->field($model, 'product_id')->hiddenInput(['id' => 'hidden_product_id_input'])->label(false) ?>
            </div>
            
            <?= $form->field($model, 'quantity')->textInput() ?>
            
            <?= $form->field($model, 'price')->textInput() ?>
            
            <?= $form->field($model, 'discount')->textInput() ?>
            
            <?= $form->field($model, 'order_id')->hiddenInput()->label(false) ?>
            
            <div class="form-group">
                <?= Html::submitButton('Добавить в заказ', ['class' => 'btn btn-info']) ?>
            </div>
        
        <?php ActiveForm::end(); ?>
              
      </div>
    </div>
  </div>
</div>