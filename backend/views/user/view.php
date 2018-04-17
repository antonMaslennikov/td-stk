<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\components\RolesHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <p>
        <?php if (Yii::$app->user->identity->role == RolesHelper::ADMIN): ?>
        
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
            
        <?php elseif (Yii::$app->user->identity->role == RolesHelper::MANAGER): ?>    
            
            <?= Html::a('Update', ['update', 'id' => Yii::$app->user->identity->id], ['class' => 'btn btn-primary']) ?>
            
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'email:email',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
