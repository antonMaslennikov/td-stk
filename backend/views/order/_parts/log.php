<?php
    use yii\helpers\Url;
    use yii\helpers\Html;

    use backend\models\Order;
?>

<ul class="timeline">

    <!-- timeline time label -->
    <?php foreach ($model->logsTimeline AS $day): ?>
    <li class="time-label">
        <span class="bg-red">
            <?= $day['date'] ?>
        </span>
    </li>
    <!-- /.timeline-label -->

    <!-- timeline item -->
    <?php foreach ($day['rows'] AS $row): ?>
    <li>
        <!-- timeline icon -->
        <?php 
            switch ($row['action']){
                case 'set_payment':
                    $ico = 'bg-yellow fa-money';
                    $row['text'] = 'Поступили оплата ' . $row['result'] . ' р.';
                    break;
                case 'edit_quantity':
                    $ico = 'bg-aqua fa-edit';
                    $row['text'] = 'Изменёно количество позиции #' . $row['info'] . ' на ' . $row['result'] . ' шт.';
                    break;
                case 'edit_discount':
                    $ico = 'bg-aqua fa-edit';
                    $row['text'] = 'Изменёна скидка позиции #' . $row['info'] . ' на ' . $row['result'];
                    break;
                case 'edit_price':
                    $ico = 'bg-aqua fa-edit';
                    $row['text'] = 'Изменёно цена позиции #' . $row['info'] . ' на ' . $row['result'] . ' р.';
                    break;
                case 'change_payment_type':
                    $ico = 'bg-purple fa-refresh';
                    $row['text'] = 'Изменён тип оплаты на "' . Order::$paymentTypes[$row['result']]['name'] . '" с "' . Order::$paymentTypes[$row['info']]['name'] . '"';
                    break;
                case 'change_delivery_date':
                    $ico = 'bg-maroon fa-truck';
                    $row['text'] = 'Изменёна дата доставки на ' . $row['result'];
                    break;
                case 'del_position':
                    $ico = 'bg-red fa-remove';
                    $row['text'] = 'Из заказа удалена позиция ' . $row['result'];
                    break;
                default:
                    $ico = 'bg-green fa-user';
                    $row['text'] = $row['result'] . ' ' . $row['info'];
                    break;
            }
        ?>
        
        <i class="fa <?= $ico ?>"></i>
        
        <!-- i class="fa fa-comments bg-yellow"></i-->
        <div class="timeline-item">
            <span class="time"><i class="fa fa-clock-o"></i> <?= $row['time'] ?></span>

            <h3 class="timeline-header"><?= Html::a($row['user']->username, Url::to(['user/view', 'id' => $row['user']->id])) ?> <?php if ($row['user']->fio) { ?><?= $row['user']->fio ?><?php } ?></h3>

            <div class="timeline-body">
                <?= $row['text'] ?>
            </div>

        </div>
    </li>
    <?php endforeach; ?>
    <!-- END timeline item -->
    <?php endforeach; ?>
    <li>
      <i class="fa fa-clock-o bg-gray"></i>
    </li>
    
</ul>