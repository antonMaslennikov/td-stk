<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Size */

$this->title = 'Добавить размер';
$this->params['breadcrumbs'][] = ['label' => 'Размеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="size-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
