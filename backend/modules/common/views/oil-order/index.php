<?php

use common\helpers\Html;
use common\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '加油订单';
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

            'id',
            // 'status',
            // 'from',
            // 'orderId',
            // 'paySn',
            // 'phone',
            [
                'label' => '用户',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "订单号：" . $model->orderId . '<br>' .
                        "支付号：" . $model->paySn . '<br>' .
                        "手机：" . $model->phone . '<br>'  ;
                },
                'format' => 'raw',
            ],
            // 'orderTime',
            // 'payTime',
            // 'refundTime',
            [
                'label' => '订单时间',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "下单时间：" . $model->orderTime . '<br>' .
                        "付款时间：" . $model->payTime . '<br>' .
                        "退款时间：" . $model->refundTime . '<br>' ;
                },
                'format' => 'raw',
            ],
            // 'province',
            // 'city',
            // 'county',
            [
                'label' => '位置',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "省份：" . $model->province . '<br>' .
                        "城市：" . $model->city . '<br>' .
                        "区县：" . $model->county . '<br>'  ;
                },
                'format' => 'raw',
            ],
            //'gunNo',
            // 'gasName',
            // 'oilNo',
            [
                'label' => '油站',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "油站：" . $model->gasName . '<br>' .
                        "油号：" . $model->oilNo . '<br>' .
                        "升数：" . $model->litre . '<br>'  ;
                },
                'format' => 'raw',
            ],
            // 'amountPay',
            // 'amountGun',
            // 'amountDiscounts',
            [
                'label' => '金额',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "总额：" . $model->amountGun . '<br>' .
                        "实付：" . $model->amountPay . '<br>' .
                        "优惠：" . ($model->amountDiscounts > 0 ? $model->amountDiscounts : $model->couponMoney ) . '<br>'  ;
                },
                'format' => 'raw',
            ],
            [
                'label' => '状态',
                'filter' => false, //不显示搜索框
                'value' => function ($model) {
                    return "订单状态：" . $model->orderStatusName . '<br>' .
                        "支付方式：" . $model->payType . '<br>' .
                        "实际单价：" . $model->priceUnit . '<br>'  ;
                },
                'format' => 'raw',
            ],
            // 'orderStatusName',
            // 'couponMoney',
            //'couponId',
            //'couponCode',
            // 'litre',
            // 'payType',
            // 'priceUnit',
            // 'priceOfficial',
            // 'priceGun',
            //'orderSource',
            //'qrCode4PetroChina',
            //'created_at',
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'header' => '操作',
            //     'template' => '{edit} {status} {delete}',
            //     'buttons' => [
            //     'edit' => function($url, $model, $key){
            //             return Html::edit(['edit', 'id' => $model->id]);
            //     },
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
