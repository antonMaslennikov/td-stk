<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    use common\models\Order;
    use backend\components\AddItemWidget;
    use backend\models\Stock;
    use backend\models\ProductionItems;
?>

<?= Html::beginForm(['order/updateitems'], 'post') ?>
                                   
    <table class="table table-bordered table-striped" role="grid" aria-describedby="example1_info">
    <tr>
        <th>#</th>
        <th colspan="2">Товар</th>
        <th>Цвет / размер</th>
        <th>Кол-во</th>
        <th>Цена</th>
        <th>Скидка</th>
        <th>Итого</th>
        <th style="width:30px">&nbsp;</th>
    </tr>
    <?php if ($model->status != Order::STATUS_DELIVERED && $model->status != Order::STATUS_CANCELED): ?>
    <tr>
        <td colspan="9">
           
            <?= Html::a('<i class="fa fa-fw fa-plus"></i> Добавить позицию', '#addItemModal', ['data-toggle' => 'modal', 'class' => 'btn btn-xs btn-success']) ?>    
           
            <?php if (Yii::$app->request->get('edit')) { ?>
                <?= Html::a('<i class="fa fa-fw fa-pencil"></i> вернуться к просмотру', Url::to(['order/view', 'id' => $model->id]), ['class' => 'pull-right btn btn-xs btn-warning']) ?>
            <?php } else { ?>
                <?= Html::a('<i class="fa fa-fw fa-pencil"></i> редактировать содержимое', Url::to(['order/view', 'id' => $model->id, 'edit' => true]), ['class' => 'pull-right btn btn-xs btn-warning']) ?>
            <?php } ?>
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
        <td><?= $g->product->color->name ?> / <?= $g->product->size->name ?></td>
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
            <a href="<?= Url::to(['order/deletepos', 'id' => $g->id]) ?>" class="btn btn-danger btn-xs delete-pos" title="Удалить товар"><i class="fa fa-fw fa-times"></i></a>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            
            <?php 
                // общее количество готовых не зарезервированных товаров на складе
                $readyQ = Stock::getReadyProductQuantity($g->product_id, 0); 
            
                // количество зарезервированных на складе позиций
                $readyReserved = Stock::getReadyProductQuantity($g->product_id, $g->id); 
            
                // количество в производстве
                $inproduction = ProductionItems::getQuantityInProduction($g->id);
            ?>
            
            <div class="row">
                <?php if ($readyQ > 0 && $readyReserved < $g->quantity && $inproduction < $g->quantity) { ?>
                <div class="col-sm-3">
                    Есть на складе: <?= $readyQ ?> шт.
                </div>
                <?php } ?>
                <?php if ($readyReserved > 0) { ?>
                <div class="col-sm-3">
                    Зарезервировано: <?= $readyReserved ?> шт.
                </div>
                <?php } ?>
                <?php if ($readyQ > 0 && ($readyReserved < $g->quantity) && ($inproduction < $g->quantity - $readyReserved)) { ?>
                <div class="col-sm-1">
                    <?= Html::a('В резерв', Url::to(['order/put2reserv', 'item_id' => $g->id]), ['class' => 'btn btn-xs btn-info']); ?>
                </div>
                <?php } ?>
                <?php if ($readyReserved < $g->quantity) { ?>
                <div class="col-sm-6">
                    <?php if ($inproduction > 0) { ?>
                        В производстве: <?= $inproduction ?> шт.
                    <?php } ?>
                    <?php if ($inproduction < $g->quantity - $readyReserved) { ?>
                        <?= Html::a('В производство: ' . ($g->quantity - $readyReserved - $inproduction) . ' шт.', Url::to(['order/put2production', 'item_id' => $g->id]), ['class' => 'btn btn-xs btn-primary']); ?>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            
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
        <th colspan="2"><?= $model->sum ?> руб.</th>
    </tr>
    <tr>
        <th style="text-align: right" colspan="7">Доставка</th>
        <th colspan="2"><?= $model->delivery_cost ?>  руб.</th>
    </tr>

    <tr>
        <th style="text-align: right" colspan="7">Оплачено</th>
        <th colspan="2"><?= $model->alreadyPayed ?> руб.</th>
    </tr>

    <?php if ($model->payment_partical > 0): ?>
    <tr>
        <th style="text-align: right" colspan="7">Оплачено купоном</th>
        <th colspan="2"><?= $model->payment_partical ?> руб.</th>
    </tr>
    <?php endif; ?>

    <tr>
        <th style="text-align: right" colspan="7">Итого к оплате</th>
        <th colspan="2"><?= max(0, $model->delivery_cost + $model->sum - $model->alreadyPayed) ?> руб.</th>
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