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
                    <li class="active"><a href="#main" data-toggle="tab">Основные данные</a></li>
                    <li><a href="#log" data-toggle="tab">История заказа</a></li>
                    <li><a href="#comments" data-toggle="tab">Комментарии к заказу</a></li>
                </ul>

                <div class="tab-content">

                    <div class="tab-pane active" id="main">

                        <div class="row">
                            <div class="col-sm-12"><h2>Статус заказ: <a href="#"><?= $model->status_name ?></a></h2></div>
                        </div>

                        <br>

                        <div class="row">
                           
                            <div class="col-sm-4">
                               
                                <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                    <tr>
                                       <th colspan="2">Данные заказчика</th>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><?= $model->client->email ?></td>
                                    </tr>
                                    <tr>
                                        <td>Имя</td>
                                        <td><?= $model->client->name ?></td>
                                    </tr>
                                    <tr>
                                        <td>Телефон</td>
                                        <td><?= $model->client->phone ?></td>
                                    </tr>
                                    <tr>
                                        <td>Дата регистрации</td>
                                        <td><?= $model->client->created_at ?></td>
                                    </tr>
                                </table>

                               
                                <form role="form" method="post" id="paymentForm">   

                                    <table id="example2" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example2_info">
                                        <tr>
                                           <th colspan="2">Оплата</th>
                                        </tr>
                                        <tr>
                                            <td>Тип оплаты</td>
                                            <td>
                                                <span><?= $model->payment_type_rus ?></span>
                                                <select name="payment_type" id="" class="hidden">
                                                    {foreach from=$paymentTypes key="k" item="pt"}
                                                    <option value="{$k}" {if $k == $order->user_basket_payment_type}selected="selected"{/if}>{$pt.title}</option>
                                                    {/foreach}
                                                </select>

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

                                </form>
                                
                                
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

                                        <?= $form->field($paymentForm, 'sum')->textInput(['value' => $model->sum + $model->delivery_cost - $model->alreadyPayed, 'placeholder' => 'укажите сумму', 'class' => 'form-control']) ?>
                                        
                                        <?= $form->field($paymentForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>
                                        
                                        <?= $form->field($paymentForm, 'payment_type')->hiddenInput(['value' => $model->payment_type])->label(false) ?>
                                        
                                        <div class="form-group">
                                            <?= Html::submitButton('Оплатить', ['class' => 'btn btn-info']) ?>
                                        </div>
                                        
                                    <?php ActiveForm::end(); ?>
                                    
                                <?php endif; ?>
                                      
                                      
                                <form role="form" method="post" id="deliveryForm">

                                    <table id="example2" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                        <tr>
                                           <th colspan="2">Доставка</th>
                                        </tr>
                                        <tr>
                                            <td>Тип доставки</td>
                                            <td>
                                                <span><?= $model->delivery_type_rus ?></span>
                                                <select name="delivery_type" id="" class="hidden">
                                                    {foreach from=$deliveryTypes key="k" item="dt"}
                                                    <option value="{$k}" {if $k == $order->user_basket_delivery_type}selected="selected"{/if}>{$dt.title}</option>
                                                    {/foreach}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Страна</td>
                                            <td>
                                                <span><?= $model->address->country ?></span>
                                                <select name="address[country]" id="" class="hidden">
                                                    {foreach from=$countries key="k" item="c"}
                                                    <option value="{$c.country_id}" {if $c.country_id == $order->address.country_id}selected="selected"{/if}>{$c.country_name}</option>
                                                    {/foreach}
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Город</td>
                                            <td>
                                                <span><?= $model->address->city ?></span>
                                                <input type="text" name="address[city]" value="{$order->address.city}" class="hidden">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Адрес</td>
                                            <td>
                                                <span><?= $model->address->address ?></span>
                                                <input type="text" name="address[address]" value="<?= $model->address->address ?>" class="hidden">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ФИО</td>
                                            <td>
                                                <span><?= $model->address->name ?></span>
                                                <input type="text" name="address[name]" value="{$order->address.name}" class="hidden">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Телефон</td>
                                            <td>
                                                <span><?= $model->address->phone ?></span>
                                                <input type="text" name="address[phone]" value="{$order->address.phone}" class="hidden">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Стоимость доставки</td>
                                            <td>
                                                <span><?= $model->delivery_cost ?> руб.</span>
                                                <input type="text" name="delivery_cost" value="<?= $model->delivery_cost ?> " class="hidden">
                                            </td>
                                        </tr>
                                        <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                        <tfoot>
                                        <tr>
                                            <th style="border-right: none">
                                               <button class="btn btn-block-sm btn-success" name="save" style="display: none">сохранить</button>
                                            </th>
                                            <th style="text-align: right;border-left: none">
                                                <button class="btn btn-block-sm btn-default toggleForm" data-form_id="deliveryForm" style="display: none">отменить</button>
                                                <button class="btn btn-block-sm btn-warning toggleForm" data-form_id="deliveryForm"><i class="fa fa-fw fa-pencil"></i>изменить</button>
                                            </th>
                                        </tr>    
                                        </tfoot>
                                        <?php endif; ?>
                                    </table>

                                </form>        
                            
                            
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
                               
                                <?= Html::beginForm(['order/updateitems'], 'post') ?>
                                   
                                    <table class="table table-bordered table-striped" role="grid" aria-describedby="example1_info">
                                    <tr>
                                        <th>#</th>
                                        <th colspan="2">Товар</th>
                                        <th>Количество</th>
                                        <th>Цена</th>
                                        <th>Скидка</th>
                                        <th>Итоговая цена</th>
                                        <th></th>
                                    </tr>
                                    <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                    <tr>
                                        <td colspan="8">
                                            <?= Html::a('<button class="btn btn-xs btn-success"><i class="fa fa-fw fa-plus"></i> Добавить позицию</button>', '#addItemModal', ['data-toggle' => 'modal']) ?>    
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (count($model->items) > 0): ?>
                                    <?php foreach ($model->items AS $k => $g): ?>
                                    <tr>
                                        <td><?= $k+1 ?></td>
                                        <td width="100">
                                            <?php if ($g->product->picture): ?>
                                            <a href="<?= Url::to(['product/view', 'id' => $g->product_id]) ?>" target="_blank">
                                            <img src="<?= $g->product->picture ?>" alt="<?= $g->product->name_ru ?>" width="100" />
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?= Url::to(['product/view', 'id' => $g->product_id]) ?>" target="_blank"><?= $g->product->name_ru ?></a><br /><?= $g->product->art ?></td>
                                        <td>
                                            <?php if (Yii::$app->request->get('edit') && $model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                                <input type="text" name="pos[<?= $g->id ?>][quantity]" size="4" value="<?= $g->quantity ?>">
                                            <?php else: ?>
                                                <?= $g->quantity ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (Yii::$app->request->get('edit') && $model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                                <input type="text" name="pos[<?= $g->id ?>][price]" size="4" value="<?= $g->price ?>">
                                            <?php else: ?>
                                                <?= $g->price ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (Yii::$app->request->get('edit') && $model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                                <input type="text" name="pos[<?= $g->id ?>][discount]" size="4" value="<?= $g->discount ?>">
                                            <?php else: ?>
                                                <?= $g->discount ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= number_format(round(($g->price - ($g->price / 100 * $g->discount)) * $g->quantity), 0, ',', ' ')  ?></td>
                                        <td style="text-align: right">
                                            <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                            <a href="<?= Url::to(['order/view', 'id' => $model->id, 'edit' => $g->id]) ?>" class="btn btn-warning btn-xs" title="Изменить данные"><i class="fa fa-fw fa-pencil"></i></a>
                                            <a href="<?= Url::to(['order/deletepos', 'id' => $g->id]) ?>" onclick="return confirm('Вы уверены?');" class="btn btn-danger btn-xs delete-js" title="Удалить товар"><i class="fa fa-fw fa-times"></i></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="10">Нет позиций в заказе</td>
                                    </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <th style="text-align: right" colspan="7">Подитог</th>
                                        <th><?= $model->sum ?>  руб.</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right" colspan="7">Доставка</th>
                                        <th><?= $model->delivery_cost ?>  руб.</th>
                                    </tr>

                                    <tr>
                                        <th style="text-align: right" colspan="7">Оплачено</th>
                                        <th><?= $model->alreadyPayed ?> руб.</th>
                                    </tr>

                                    <?php if ($model->payment_partical > 0): ?>
                                    <tr>
                                        <th style="text-align: right" colspan="7">Оплачено купоном</th>
                                        <th><?= $model->payment_partical ?> руб.</th>
                                    </tr>
                                    <?php endif; ?>

                                    <tr>
                                        <th style="text-align: right" colspan="7">Итого к оплате</th>
                                        <th><?= max(0, $model->delivery_cost + $model->sum - $model->alreadyPayed) ?> руб.</th>
                                    </tr>


                                    <?php if (Yii::$app->request->get('edit') && $model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
                                    <tfoot>
                                        <tr>
                                            <td colspan="20" style="text-align: right">
                                                <button class="btn btn-block-sm btn-success" name="savepos">сохранить</button>
                                                <a href="<?= Url::to(['order/view', 'id' => $model->id]) ?>" class="btn btn-block-sm btn-default margin">отменить</a>

                                                <input type="hidden" name="order_id" value="<?= $model->id ?>">
                                            </td>
                                        </tr>
                                    </tfoot>
                                    <?php endif; ?>
                                    </table>
                                
                                <?= Html::endForm() ?>
                                
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane" id="log">
                        <table class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                        <tr>
                            <td><b>#</b></td>
                            <td><b>Событие</b></td>
                            <td><b>Результат</b></td>
                            <td><b>Инфо</b></td>
                            <td><b>Дата</b></td>
                        </tr>
                        <?php foreach ($model->logs AS $k => $l): ?>
                        <tr>
                            <td><?= $k + 1 ?></td>
                            <td><?= $l->action ?></td>
                            <td><?= $l->result ?></td>
                            <td><?= $l->info ?></td>
                            <td><?= $l->time ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </table>
                    </div>


                    <div class="tab-pane" id="comments">
                       <div class="row">
                            <div class="col-sm-5">

                                <h4>Добавить комментарий</h4>

                                <?php $form = ActiveForm::begin(['action' => Url::to(['order/addcomment'])]); ?>
                                   
                                    <?= $form->field($commentForm, 'text')->textarea(['rows' => 7]) ?>
                                    
                                    <?= $form->field($commentForm, 'for')->dropDownList(\backend\models\OrderCommentForm::getForList()) ?>
                                    
                                    <?= $form->field($commentForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>
                                    
                                    <div class="form-group">
                                        <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
                                    </div>
                                
                                <?php ActiveForm::end(); ?>

                            </div>

                            <div class="col-sm-7">
                                <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                                <?php foreach($model->comments AS $c): ?>
                                <tr>
                                    <td>
                                        <b><?= \backend\models\OrderCommentForm::getForList()[$c->for] ?></b> <em class="pull-right"><?= $c->time ?></em><br>
                                        <?= $c->text ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


        </div>
    </div>

    <?= AddItemWidget::widget(['order' => $model]) ?>
    
    <?= CreateDocumentWidget::widget(['type' => Document::TYPE_BILL, 'order' => $model]) ?>
    
    <script>
        /*
        $('.toggleForm').click(function(){

            var fid = $('#' + $(this).data('form_id'));

            fid.find('span').toggle();
            fid.find('input, select, button').toggle();

            return false;
        });

        $('table').on('click', '.delete-js',  function(e){

            if (!confirm('Вы действительно желаете удалить товар из заказа?')) {

                e.preventDefault();
                return false;
            }

            return true;
        })
        */
    </script>
    
    <?php $this->registerCss('
        .main-box {border-top:0} 
        .main-box .box-body {padding:0}
        
        input.hidden, select.hidden {
            display: none;
        }
    ') ?>
    
</div>
