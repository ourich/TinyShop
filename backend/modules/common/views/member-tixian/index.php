<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;
use common\enums\PayTypeEnum;
use common\enums\StatusEnum;
use addons\TinyShop\common\enums\TixianStatusEnum;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现申请';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['edit']) ?>
                </div>
            </div>
            <div class="box-body table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'visible' => false,
            ],

            'id',
            [
                'attribute' => 'member.mobile',
                'label'=> '持有人',
                'filter' => Html::activeTextInput($searchModel, 'member.mobile', [
                        'class' => 'form-control'
                    ]
                ),
            ],
            [
                'label' => '提现渠道',
                'value' => function ($model) {
                    return PayTypeEnum::getValue($model->type);
                },
                'filter' => Html::activeDropDownList($searchModel, 'type', PayTypeEnum::thirdParty(), [
                        'prompt' => '全部',
                        'class' => 'form-control'
                    ]
                ),
                'format' => 'raw',
            ],
            // 'type',
            'money',
            'fee',
            // 'account',
            // 'account_img',
            // 'name',
            // 'bank_name',
            'mobile',
            'remark',
            // 'status',
            [
                'label' => '状态',
                'value' => function ($model) {
                    if ($model->status == TixianStatusEnum::PAYED) {
                        return '<span class="label label-primary">已打款</span>';
                    }elseif ($model->status == TixianStatusEnum::BOHUI) {
                        return '<span class="label label-danger">已驳回</span>';
                    } else {
                        return '<span class="label label-default">待处理</span>';
                    }
                },
                'filter' => Html::activeDropDownList($searchModel, 'status', TixianStatusEnum::getMap(), [
                        'prompt' => '全部',
                        'class' => 'form-control'
                    ]
                ),
                'format' => 'raw',
            ],
            // 'created_at',
            [
                'attribute' => 'created_at',
                'filter' => false, //不显示搜索框
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {status} {delete}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['edit', 'id' => $model->id]);
                }
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
