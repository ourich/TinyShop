<?php

use common\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model addons\TinyShop\common\models\common\OilStations */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Oil Stations';
$this->params['breadcrumbs'][] = ['label' => 'Oil Stations', 'url' => ['index']];
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
                    <?= $form->field($model, 'gasId')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'gasName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'gasLogoSmall')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'gasAddress')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'provinceName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'cityName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'countyName')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'status')->radioList(\common\enums\StatusEnum::getMap()) ?>
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
