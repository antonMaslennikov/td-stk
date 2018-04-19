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