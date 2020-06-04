<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\TinyShop\common\models\gas\GasCard */
/* @var $form yii\widgets\ActiveForm */

$this->title = '分配卡片';
$this->params['breadcrumbs'][] = ['label' => '油卡列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">分配卡片</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'give_to')->textInput() ?>
                    <?= $form->field($model, 'give_num')->textInput() ?>
                    <?= $form->field($model, 'give_begin')->textInput() ?>
                    <?= $form->field($model, 'give_end')->textInput(['readonly' => 'readonly']) ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("input[name='GasCardForm[give_begin]']").blur(function () {
        var give_begin = $("input[name='GasCardForm[give_begin]']").val();
        var give_num = $("input[name='GasCardForm[give_num]']").val();
        var give_end = Number(give_begin) + Number(give_num) -1;
        $("input[name='GasCardForm[give_end]']").val(give_end);
    });
</script>
