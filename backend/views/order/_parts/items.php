<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    use common\models\Order;
    use backend\components\AddItemWidget;

?>

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
            <a href="<?= Url::to(['order/deletepos', 'id' => $g->id]) ?>" class="btn btn-danger btn-xs delete-pos" title="Удалить товар"><i class="fa fa-fw fa-times"></i></a>
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