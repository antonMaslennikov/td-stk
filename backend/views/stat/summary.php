<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\models\Order;

$this->title = 'Сводный отчёт';
$this->params['breadcrumbs'][] = ['label' => 'Статистика', 'url' => ['/stat']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
   
    <?= Html::beginForm([''], 'get') ?>
    
    <div class="form-group">
        <label>Дата:</label>

        <div class="input-group">
            <div class="input-group-addon">
               <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control pull-right" id="reservation" name="daterange" value="<?= Yii::$app->request->get('daterange') ?>">
        </div>
    </div>
    
    <div class="form-group">
        <input type="submit" value="Показать" class="btn btn-default">
    </div>
    
    <?= Html::endForm(); ?>
    
    <? $this->registerJs("$('#reservation').daterangepicker()"); ?>
    
    <?php if (Yii::$app->request->get('daterange')): ?>
        <?php if (count($data) > 0): ?>
        <table class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <tr>
            <th>Тип доставки</th>
            <th>кол-во заказов</th>
            <th>сумма</th>
            <th>кол-во шт</th>
        </tr>
        <?php foreach($data AS $row): ?>
        <tr>
            <td><?= Order::$deliveryTypes[$row['delivery_type']]['name'] ?></td>
            <td><?= $row['c'] ?></td>
            <td><?= $row['s'] ?></td>
            <td><?= $row['q'] ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>За указанный период данных не обнаружено</p>
        <?php endif; ?>
    <?php endif; ?>
</div>