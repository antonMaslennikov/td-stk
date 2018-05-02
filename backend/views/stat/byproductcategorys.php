<?php

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'По видам товаров';
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
            <th rowspan="2">&nbsp;</th>
            <th colspan="2">муж</th>
            <th colspan="2">жен</th>
            <th colspan="2">дет</th>
            <th colspan="2">unisex</th>
            <th colspan="2">итого</th>
        </tr>
        <tr>
            <th>шт.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
            <th>руб.</th>
        </tr>
        <?php foreach($data AS $cid => $cat): ?>
        <tr>
            <td><?= $cats[$cid] ?></td>
            <td><?= $cat[0]['q'] ?></td>
            <td><?= $cat[0]['s'] ?></td>
            <td><?= $cat[1]['q'] ?></td>
            <td><?= $cat[1]['s'] ?></td>
            <td><?= $cat[2]['q'] ?></td>
            <td><?= $cat[2]['s'] ?></td>
            <td><?= $cat[3]['q'] ?></td>
            <td><?= $cat[3]['s'] ?></td>
            <td><?= $cat['total']['q'] ?></td>
            <td><?= $cat['total']['s'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th>Итого</th>
            <th><?= $total[0]['q'] ?></th>
            <th><?= $total[0]['s'] ?></th>
            <th><?= $total[1]['q'] ?></th>
            <th><?= $total[1]['s'] ?></th>
            <th><?= $total[2]['q'] ?></th>
            <th><?= $total[2]['s'] ?></th>
            <th><?= $total[3]['q'] ?></th>
            <th><?= $total[3]['s'] ?></th>
            <th><?= $total['total']['q'] ?></th>
            <th><?= $total['total']['s'] ?></th>
        </tr>
        </table>
        <?php else: ?>
            <p>За указанный период данных не обнаружено</p>
        <?php endif; ?>
    <?php endif; ?>
</div>