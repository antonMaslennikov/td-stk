<?php

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'По менеджерам';
$this->params['breadcrumbs'][] = ['label' => 'Статистика', 'url' => ['/stat']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div>

    <p>
    <?php foreach (range(date('Y'), date('Y') - 10) AS $y): ?>
    
        <?php if ($y < 2017) continue; ?>
        
        <?= Html::a($y, Url::to(['stat/bymanagers', 'year' => $y]), ['class' => 'label ' . ((!Yii::$app->request->get('year') && $y == date('Y')) || Yii::$app->request->get('year') == $y ? 'label-danger' : 'label-info')]) ?>
        
    <?php endforeach; ?>
    </p>
    
    <?php if (count($data) > 0): ?>
        <table class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
            <tr>
                <th rowspan="2"><?= $year ?></th>
                <?php foreach ($managers AS $m): ?>
                <th colspan="2"><?= $m['name'] ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php foreach ($managers AS $m): ?>
                <th>Кол-во заказов</th>
                <th>Сумма</th>
                <?php endforeach; ?>
            </tr>
            <?php foreach ($data AS $row): ?>
            <tr>
                <td><b><?= $row['name'] ?></b></td>
                <?php foreach ($row['managers'] AS $m): ?>
                <td><?= $m['c'] ?></td>
                <td><?= $m['s'] ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td>&nbsp;</td>
                <?php foreach ($managers AS $m): ?>
                <th><?= $m['total_q'] ?></th>
                <th><?= $m['total_s'] ?></th>
                <?php endforeach; ?>
            </tr>
        </table>
        
        <p><em>* Отчёт сформирован по датам оплаты заказов</em></p>
    <?php else: ?>
        <p>За указанный период данных не обнаружено</p>
    <?php endif; ?>

</div>