<?php

    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\ActiveForm;

    use common\models\Order;

    $commentForm = new \backend\models\OrderCommentForm;
?>

<div class="row">
    <div class="col-sm-5">

        <h4>Добавить комментарий</h4>

        <?php $form = ActiveForm::begin(['action' => Url::to(['order/addcomment'])]); ?>

            <?= $form->field($commentForm, 'text')->textarea(['rows' => 7]) ?>

            <?= $form->field($commentForm, 'for')->dropDownList(\backend\models\OrderCommentForm::getForList()) ?>

            <?= $form->field($commentForm, 'order_id')->hiddenInput(['value' => $model->id])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

    <div class="col-sm-7">
        <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
        <?php foreach($model->comments AS $c): ?>
        <tr>
            <td>
                <b><?= \backend\models\OrderCommentForm::getForList()[$c->for] ?></b> <em class="pull-right"><?= $c->time ?></em><br>
                <?= $c->text ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>