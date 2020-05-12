<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\TinyShop\common\models\common\OilCard */
/* @var $form yii\widgets\ActiveForm */

$this->title = '导出油卡';
$this->params['breadcrumbs'][] = ['label' => '导出油卡', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">导出油卡</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'giveNum')->label('导出数量')->textInput(); ?>
                    <?= $form->field($model, 'cardNo')->label('起始卡号')->textInput()->hint('系统自动识别的还未分配的卡号起点'); ?>
                    <?= $form->field($model, 'endNo')->textInput(['readonly' => 'readonly'])->hint('根据起点和数量计算出的终点'); ?>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">确认导出</button>
                        <span class="btn btn-white" onclick="history.go(-1)">返回</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("input[name='OilCardForm[endNo]']").click(function () {
        var val = $("input[name='OilCardForm[cardNo]']").val();
        var num = $("input[name='OilCardForm[giveNum]']").val();
        var end = Number(val) + Number(num) -1;
        $("input[name='OilCardForm[endNo]']").val(end);
    });
</script>
