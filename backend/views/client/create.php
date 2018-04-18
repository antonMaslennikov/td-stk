<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\OrderClient */

$this->title = 'Добавить нового клиента';
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-client-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
