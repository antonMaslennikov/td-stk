<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use backend\models\Document;
    
    if (Yii::$app->request->get('tab') == 'docs')
    {
        $rs = Document::find()
                    ->where(['client_id' => $model->client_id])
                    ->orderBy(['date' => SORT_DESC])
                    ->all();
        
        foreach ($rs AS $row)
        {
            $docs[$row->order_id][] = $row;
        }
    

        \Yii::$app->formatter->locale = 'ru-RU';

?>

        <p><?= Html::a('Реквизиты клиента', Url::to(['client/view', 'id' => $model->client_id]), ['class' => 'btn btn-primary']) ?></p>
       
        <table class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <th>Заказ</th>
        <th>Документ</th>
        <th>Номер</th>
        <th>Сумма</th>
        <th>Оплата</th>
        <th>Дата</th>
        <?php if (count($docs) == 0): ?>
            <tr>
                <td colspan="10">Документы отсутствуют</td>
            </tr>
        <?php else: ?>

            <?php
                $docTypesClasses = [
                    Document::TYPE_BILL => 'success',
                    Document::TYPE_AKT => 'warning',
                    Document::TYPE_NAKL => 'info',
                ]
            ?>
           
            <?php foreach($docs AS $i => $o): ?>

                <?php $k = 0; ?>

                <?php foreach($o AS $di => $d): ?>
                <tr>
                    <?php if ($k == 0) { ?>
                        <td rowspan="<?= count($o) ?>" style="background:#fff"><?= Html::a($i, Url::to(['order/view', 'id' => $i])) ?></td>
                    <? } ?>
                    <td><span class="label label-<?= $docTypesClasses[$d->type] ?>"><?= Document::getTypes()[$d->type] ?></span> <?= Html::a($d->name, Url::to(['document/view', 'id' => $d->id]), ['target' => '_blank']) ?></td>
                    <td><?= $d->number ?></td>
                    <td><?= $d->sum ?></td>
                    <td>
                        <?php if ($d->type == Document::TYPE_BILL): ?>
                            <?= Html::tag('span', $d->payed > 0 ? 'оплачен' : 'не оплачен', ['class' => 'label label-' . ($d->payed > 0 ? 'success' : 'danger')]) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= \Yii::$app->formatter->asDate($d->date) ?></td>
                </tr>

                <? $k++ ?>

                <?php endforeach; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        </table>


<?php } ?>