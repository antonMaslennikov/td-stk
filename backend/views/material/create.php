<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Material */

$this->title = 'Добавить новый материал';
$this->params['breadcrumbs'][] = ['label' => 'Виды ткани', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
