<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '油站列表';
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
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'visible' => false,
            ],

            //'id',
            'gasId',
            // 'gasName',
            [
                'attribute' => 'gasName',
                'headerOptions' => ['class' => 'col-md-2'],
            ],
            [
                'attribute' => 'channelId',
                'label'=> '渠道',
                'headerOptions' => ['class' => 'col-md-1'],
                'value' => function ($model) {
                    return empty($model['channelId']) ? '团油' : '小桔';
                },
            ],
            // 'channelId',
            // 'gasType',
            //'gasLogoBig',
            // 'gasLogoSmall',
            'gasAddress',
            //'gasAddressLongitude',
            //'gasAddressLatitude',
            //'provinceCode',
            //'cityCode',
            //'countyCode',
            'provinceName',
            'cityName',
            'countyName',
            //'isInvoice',
            'companyId',
            'created_at:datetime',
            //'status',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{edit} {status} {delete}',
                'buttons' => [
                'edit' => function($url, $model, $key){
                        return Html::edit(['edit', 'id' => $model->id]);
                },
               'status' => function($url, $model, $key){
                        return Html::status($model['status']);
                  },
                'delete' => function($url, $model, $key){
                        return Html::delete(['delete', 'id' => $model->id]);
                },
                ]
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>