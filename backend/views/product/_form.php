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
            <li><a href="#image" data-toggle="tab">Изображения</a></li>
            <li><a href="#stock" data-toggle="tab">Цены</a></li>
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

                        <?= $form->field($model, 'material_id')->dropDownList(Material::getList(), ['prompt' => 'Выберите из списка',]) ?>

                        <?= $form->field($model, 'color_id')->widget(ColorPickerWidget::className()); ?>

                        <?= $form->field($model, 'size_id')->dropDownList(Size::getList(), ['prompt' => 'Выберите из списка',]) ?>
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
        </div>
 
        <div class="box-footer">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-info']) ?>
            <?= Html::submitButton('Применить', ['class' => 'btn btn-success', 'name' => 'apply']) ?>
            <?= Html::a('Отмена', Url::to(['product/index']), ['class' => 'btn btn-default pull-right']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <?php $this->registerCss('.main-box {border-top:0} .main-box .box-body {padding:0}') ?>
    

</div>