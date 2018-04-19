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
</table>