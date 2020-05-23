<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提货申请';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
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

            'id',
            // 'status',
            // 'type',
            'member_id',
            // 'cardNo',
            'cardNum',
            'name',
            'mobile',
            'address',
            // 'created_at',
            'reply',
            [
                // 'label' => '创建时间',
                'attribute' => 'created_at',
                'filter' => false, //不显示搜索框
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
            [
                'header' => "操作",
                'class' => 'yii\grid\ActionColumn',
                'template' => '{edit} {status} {delete}',
                'buttons' => [
                    'edit' => function ($url, $model, $key) {
                        return Html::edit(['ajax-edit', 'id' => $model['id']], '快递单号', [
                            'data-toggle' => 'modal',
                            'data-target' => '#ajaxModal',
                        ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        return Html::delete(['destroy', 'id' => $model->id]);
                    },
                ],
            ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
