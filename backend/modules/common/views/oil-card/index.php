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
                    <span class="btn btn-white btn-sm" onclick="add(this)" num='5'>增发</span>
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
            'member_id',
            'user',
            // 'created_at',
            'end_at',
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
                    rfAffirm(data.message);
                } else {
                    rfAffirm(data.message);
                }
            }
        });
    }
</script>
