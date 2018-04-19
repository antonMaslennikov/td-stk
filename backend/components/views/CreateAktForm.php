<?php

    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    use yii\web\JsExpression;
    use kartik\date\DatePicker;
    use backend\models\Document;
?>

<div id="add<?php if ($model->type == Document::TYPE_AKT): ?>Akt<?php else: ?>Nakl<?php endif; ?>Modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Заголовок модального окна -->
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Добавить <?php if ($model->type == Document::TYPE_AKT): ?>новый акт<?php else: ?>новую накладную<?php endif; ?></h4>
      </div>
      <!-- Основное содержимое модального окна -->
      <div class="modal-body">
        
        <?php $form = ActiveForm::begin(['action' => Url::to(['document/create'])]); ?>
            
            <?= $form->field($model, 'name')->textInput() ?>

            <div class="row">
                <div class="col-sm-4"><?= $form->field($model, 'number')->textInput(['value' => $model->getNextNumber()]) ?></div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                        'removeButton' => false,
                        'options' => ['placeholder' => 'Дата', 'value' => date('Y-m-d')],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                        ]
                    ]);
                    ?>
                </div>
            </div>
            
            <label>Позиции</label>
            
            <table class="billform-positions">
            <tr>
                <th></th>
                <th>&nbsp;</th>
                <th width="80">Кол-во</th>
                <th>&nbsp;</th>
                <th width="80">Цена</th>
            </tr>
            <tbody>
            <?php if ($parent): ?>
                <?php foreach ($parent->positions AS $k => $i): ?>
                <tr>
                    <td>
                        <?= Html::activeInput('text', $model, 'positions[' . $k . '][name]', ['value' => $i->name]) ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <?= Html::activeInput('text', $model, 'positions[' . $k . '][q]', ['value' => $i->quantity]) ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <?= Html::activeInput('text', $model, 'positions[' . $k . '][p]', ['value' => $i->price]) ?>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="#" class="btn btn-danger btn-xs delete-js pull-right" title="Удалить позицию"><i class="fa fa-fw fa-times"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php foreach (range(100, 120) AS $k): ?>
            <tr class="<?php if ($parent || $k > 100): ?>hidden-row<? endif; ?>">
                <td>
                    <?= Html::activeInput('text', $model, 'positions[' . $k . '][name]', ['placeholder' => 'Позиция']) ?>
                </td>
                <td>&nbsp;</td>
                <td>
                    <?= Html::activeInput('text', $model, 'positions[' . $k . '][q]', ['placeholder' => 'шт.']) ?>
                </td>
                <td>&nbsp;</td>
                <td>
                    <?= Html::activeInput('text', $model, 'positions[' . $k . '][p]', ['placeholder' => 'руб.']) ?>
                </td>
                <td>&nbsp;</td>
                <td>
                    <a href="#" class="btn btn-danger btn-xs delete-js pull-right" title="Удалить позицию"><i class="fa fa-fw fa-times"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
            
            <p><a href="#" id="addPosition">Добавить позицию</a></p>
            
            <br>
            
            <div class="form-group">
               
                <?= $form->field($model, 'parent_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'type')->hiddenInput()->label(false) ?>
                
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-info']) ?>
            </div>
        
        <?php ActiveForm::end(); ?>
              
          <style>
              .billform-positions {
                  width:100%;
              }
              .billform-positions input {
                  width:100%;
                  padding:2px 6px;
              }
              .billform-positions tr td {
                padding-bottom:8px;  
              } 
              
              .billform-positions tr.hidden-row {
                  display:none;
              }
          </style>
             
        <?php $this->registerJs("
        $('#addPosition').click(function() {
            $('.billform-positions tr.hidden-row').eq(0).removeClass('hidden-row');
            return false;
        });
            
        $('.delete-js').click(function() {
            $(this).parent().parent().remove();
            return false;
        });
        ");
        ?>
              
      </div>
    </div>
  </div>
</div>