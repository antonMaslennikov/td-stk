<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

use common\models\Product;
use common\models\Category;
use common\models\Material;
use backend\models\Size;

use backend\components\ColorPickerWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
            <li class="active"><a href="#main" data-toggle="tab">Основные данные</a></li>
            <li><a href="#options" data-toggle="tab">Характеристики</a></li>
            <li><a href="#image" data-toggle="tab">Изображения</a></li>
            <li><a href="#stock" data-toggle="tab">Цены</a></li>
            <li><a href="#production" data-toggle="tab">Печать и пошив</a></li>
        </ul>
   
        <div class="tab-content">
            
            <div class="tab-pane active" id="main">
                <div class="row">
                    <div class="col-sm-6">  
                        <?= $form->field($model, 'name_ru')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'slug')->textInput(['maxlength' => true, 'placeholder' => 'Заполняется само если не указывать']) ?>

                        <?= $form->field($model, 'art')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>
                       
                        <?= $form->field($model, 'status')->dropDownList(Product::getStatusList()) ?>
                    </div>  
                    <div class="col-sm-6">
                        <?= $form->field($model, 'category_id')->dropDownList(Category::getAllTree()) ?>

                        <?= $form->field($model, 'color_id')->widget(ColorPickerWidget::className()); ?>

                        <?= $form->field($model, 'size_id')->dropDownList(Size::getList(), ['prompt' => 'Выберите из списка',]) ?>
                        
                        <?= $form->field($model, 'sex')->dropDownList(Product::getSexList(), ['prompt' => 'Выберите из списка',]) ?>
                        
                        <br />
                        
                        <?= $form->field($model, 'design_id')->dropDownList([1 => 'Есть', 0 => 'Чистое']) ?>
                    </div>
                </div> 
            </div>
            
            <div class="tab-pane" id="image">
            
                <?= $form->field($model, 'pictures[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                
                <p><small>можно загружать сразу несколько изображений</small></p>
            
                <?php if (count($product->pictures) > 0): ?>
                <div class="product-pics clearfix">
                    <?php foreach ($product->pictures AS $k => $p): ?>
                        <div>
                            <a href="<?= $p->path ?>" data-fancybox><img src="<?= $p->thumb ?>" /></a>
                            <a href="<?= Url::to(['product/deletepicture', 'id' => $p->id]) ?>" class="delete" onclick="return confirm('Уверены что хотите удалить данное изображение?');"><img src="/admin/img/icons/delete.png" /></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
            </div>
            
            <div class="tab-pane" id="stock">
                
                <?= $form->field($model, 'selfprice')->textInput() ?>
                
                <?= $form->field($model, 'price')->textInput() ?>
                
                <?= $form->field($model, 'price_final')->textInput(['value' => $model->price - ($model->price / 100 * $model->discount) ]) ?>
                
            </div>
            
            <div class="tab-pane" id="options">
                
                <?= $form->field($model, 'weight')->textInput() ?>
                
                <?= $form->field($model, 'width')->textInput() ?>
                
                <?= $form->field($model, 'height')->textInput() ?>
                
                <?= $form->field($model, 'length')->textInput() ?>
                                
                <?= $form->field($model, 'quantityInbox')->textInput() ?>
                
            </div>
        
            <div class="tab-pane" id="production">
                <div class="row">
                    <div class="col-sm-3">
                        <h4>Пошив</h4>
                        <?= $form->field($model, 'sew_base')->dropDownList(Material::getList(), ['prompt' => 'Выберите из списка',]) ?>

                        <?= $form->field($model, 'sew_rubber')->textInput() ?>

                        <?= $form->field($model, 'sew_thread')->textInput() ?>

                        <?= $form->field($model, 'sew_label')->textInput() ?>
                    </div>
                    <div class="col-sm-3">
                        <h4>Печать</h4>
                        
                        <?= $form->field($model, 'print_type')->dropDownList([0 => 'Шелкография']) ?>
                        
                        <?= $form->field($model, 'print_colors')->textInput() ?>
                        
                        <?= $form->field($model, 'print_expense')->textInput() ?>
                        
                        <?= $form->field($model, 'print_more_material')->textInput() ?>
                        
                        <?= $form->field($model, 'print_panton_numbers')->textInput() ?>
                    </div>
                </div>
                
            </div>
        </div>
 
        <div class="box-footer">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton('Применить', ['class' => 'btn btn-success', 'name' => 'apply']) ?>
            <?= Html::a('Отмена', Url::to(['product/index']), ['class' => 'btn btn-default pull-right']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <?php $this->registerCss('.main-box {border-top:0} .main-box .box-body {padding:0}') ?>
    

</div>
