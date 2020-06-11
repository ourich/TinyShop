<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;
use common\enums\PayTypeEnum;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\TixianStatusEnum;

/* @var $this yii\web\View */
/* @var $model addons\TinyShop\common\models\common\MemberTixian */
/* @var $form yii\widgets\ActiveForm */

$this->title = '详情';
$this->params['breadcrumbs'][] = ['label' => '提现申请', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">基本信息</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'fieldConfig' => [
                        'template' => "<div class='col-sm-2 text-right'>{label}</div><div class='col-sm-10'>{input}\n{hint}\n{error}</div>",
                    ],
                ]); ?>
                <div class="col-sm-12">
                    <?= $form->field($model, 'member_id')->textInput() ?>
                    <?= $form->field($model, 'money')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'fee')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'type')->dropDownList(PayTypeEnum::thirdParty()) ?>
                    <?= $form->field($model, 'account_img')->widget(\common\widgets\webuploader\Files::class, [
                            'type' => 'images',
                            'theme' => 'default',
                            'themeConfig' => [],
                            'config' => [
                                // 可设置自己的上传地址, 不设置则默认地址
                                // 'server' => '',
                                'pick' => [
                                    'multiple' => false,
                                ],
                            ]
                    ]); ?>
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'account')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'status')->radioList(TixianStatusEnum::getMap()); ?>
                    <?= $form->field($model, 'remark')->textarea() ?>
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
