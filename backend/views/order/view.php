<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\models\Order;
use backend\models\Document;
use backend\components\AddItemWidget;
use backend\components\CreateDocumentWidget;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = 'Заказ №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

   <div class="row">
        <div class="col-sm-12">

            <div class="nav-tabs-custom ">

                <ul class="nav nav-tabs">
                    <li <?php if (!Yii::$app->request->get('tab')) { ?>class="active"<?php } ?>><a href="#main" data-toggle="tab">Основные данные</a></li>
                    <li><a href="#log" data-toggle="tab">История заказа</a></li>
                    <li><a href="#comments" data-toggle="tab">Комментарии к заказу</a></li>
                    <li <?php if (Yii::$app->request->get('tab') == 'docs') { ?>class="active"<?php } ?>><?= Html::a('Документы по клиенту', Url::to(['view', 'id' => $model->id, 'tab' => 'docs'])) ?></li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane <?php if (!Yii::$app->request->get('tab')) { ?>active<?php } ?>" id="main">

                        <div class="row">
                            <div class="col-sm-12"><h2>Статус заказ: <a href="#"><?= $model->status_name ?></a></h2></div>
                        </div>

                        <br>

                        <div class="row">
                           
                            <div class="col-sm-4">
                               
                                <?= $this->render('_parts/client', ['model' => $model]) ?>

                                <?= $this->render('_parts/delivery', ['model' => $model]) ?>
                               
                                <?= $this->render('_parts/payment', ['model' => $model]) ?>
                                
                                <form action="" method="post">

                                    <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                        <p><b>Операции с заказом</b></p>

                                        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_PREPARED && $model->status != Order::STATUS_CANCELED): ?>
                                            <button type="submit" class="btn btn-block btn-primary" name="ch-status" value="prepared">Заказ подготовлен</button>
                                        <?php endif; ?>

                                        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                            <button type="submit" class="btn btn-block btn-success" name="ch-status" value="delivered">Заказ доставлен</button>
                                        <?php endif; ?>

                                        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                            <button type="submit" class="btn btn-block btn-danger" name="ch-status" value="canceled" onclick="return confirm('Вы уверены?');">Отменить заказ</button>
                                        <?php endif; ?>

                                        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                            <!-- button type="submit" class="btn btn-block btn-success" name="ch-status" value="ordered">Откатить заказ</button -->
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                 </form>    

                            </div>
                        
                            <div class="col-sm-8">
                               
                                <?= $this->render('_parts/items', ['model' => $model]) ?>
                                
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="log">
                       
                        <?= $this->render('_parts/log', ['model' => $model]) ?>
                        
                    </div>


                    <div class="tab-pane" id="comments">
                       
                        <?= $this->render('_parts/comments', ['model' => $model]) ?>
                       
                    </div>
                    
                    <div class="tab-pane <?php if (Yii::$app->request->get('tab') == 'docs') { ?>active<?php } ?>" id="docs">
                        
                        <?= $this->render('_parts/docs', ['model' => $model]) ?>
                        
                    </div>

                </div>

            </div>


        </div>
    </div>

    <?= AddItemWidget::widget(['order' => $model]) ?>
    
    <?= CreateDocumentWidget::widget(['type' => Document::TYPE_BILL, 'order' => $model]) ?>
    
    <?php 
        $this->registerJs("
            $(document).ready(function(){
                $('.toggleForm').click(function(){

                    var fid = $('#' + $(this).data('form_id'));

                    fid.find('td > span, button').toggle();
                    fid.find('.hh').toggleClass('hidden');

                    return false;
                });

                $('.delete-pos').click(function(){

                    if (!confirm('Вы действительно желаете удалить товар из заказа?')) {
                        return false;
                    }

                    return true;
                })
            });
        ");
    
        $this->registerCss('
            .main-box {border-top:0} 
            .main-box .box-body {padding:0}

            input.hidden, select.hidden {
                display: none;
            }
        ');
    ?>
    
</div>
