<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\models\Order;

?>

<?= Html::beginForm(['order/chstatus'], 'post') ?>

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

    <?= Html::input('hidden', 'id', $model->id) ?>

<?= Html::endForm() ?>