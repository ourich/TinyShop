<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '卡片管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
                <div class="box-tools">
                    <?= Html::create(['send'], '分配生成') ?>
                    <?= Html::create(['print'], '导出印刷') ?>
                    <?= Html::create(['shiti'], '印刷标记') ?>
                    <?= Html::create(['change'], '交换预留') ?>
                    <?= Html::create(['fenpei'], '重新分配') ?>
                    <!-- <span class="btn btn-white btn-sm" onclick="add(this)" num='10000'>增发油卡</span> -->
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
            'cardNo',
            'code',
            //'status',
            //'type',
            // 'member_id', 
            [
                'attribute' => 'print',
                // 'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return $model->print ? '是' : '否';
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'col-md-1'],
            ],
            [
                'attribute' => 'member.mobile',
                'label'=> '持有人',
                'filter' => Html::activeTextInput($searchModel, 'member.mobile', [
                        'class' => 'form-control'
                    ]
                ),
            ],
            [
                'attribute' => 'user',
                'value' => 'user0.mobile',
                'filter' => false, //不显示搜索框
            ],
            [
                'attribute' => 'end_at',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return $model->end_at ? Yii::$app->formatter->asDatetime($model->end_at) : '---';
                },
                'format' => 'raw',
            ],
            // 'user',
            // 'created_at',
            // 'end_at:datetime',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'header' => '操作',
            //     'template' => '{edit} {status} {delete}',
            //     'buttons' => [
            //     // 'edit' => function($url, $model, $key){
            //     //         return Html::edit(['edit', 'id' => $model->id]);
            //     // },
            //    'status' => function($url, $model, $key){
            //             return Html::status($model['status']);
            //       },
            //     'delete' => function($url, $model, $key){
            //             return Html::delete(['delete', 'id' => $model->id]);
            //     },
            //     ]
            // ]
    ]
    ]); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function add(obj) {
        // 获取表单内信息
        let num = $(obj).attr('num');
        $.ajax({
            type: "post",
            url: "<?= Url::to(['add'])?>",
            dataType: "json",
            data: {
                num: num
            },
            success: function (data) {
                if (data.code === 200) {
                    rfSuccess(data.message);
                } else {
                    rfSuccess(data.message);
                }
            }
        });
    }
</script>
