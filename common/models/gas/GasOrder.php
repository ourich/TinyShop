<?php

namespace addons\TinyShop\common\models\gas;

use Yii;

/**
 * This is the model class for table "{{%gas_order}}".
 *
 * @property int $id 自增ID
 * @property int $status 状态
 * @property int $from 渠道
 * @property string $orderId 订单号
 * @property string $paySn 支付号
 * @property string $phone 手机号
 * @property string $orderTime 生成时间
 * @property string $payTime 支付时间
 * @property string $refundTime 退款时间
 * @property string $gasName 油站名称
 * @property string $province 省名称
 * @property string $city 市名称
 * @property string $county 县名称
 * @property int $gunNo 枪号
 * @property string $oilNo 油号
 * @property string $amountPay 实付
 * @property string $amountGun 总金额
 * @property string $amountDiscounts 优惠金额
 * @property string $orderStatusName 订单状态
 * @property string $couponMoney 优惠券金额
 * @property int $couponId 优惠券编号
 * @property string $couponCode 优惠券Code
 * @property string $litre 升数
 * @property string $payType 支付方式
 * @property string $priceUnit 最终单价
 * @property string $priceOfficial 国标价
 * @property string $priceGun 枪价
 * @property string $orderSource 渠道编码
 * @property string $qrCode4PetroChina 渠道编码
 * @property string $gasId 油站ID
 * @property int $created_at 同步时间
 */
class GasOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%gas_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'from', 'gunNo', 'couponId', 'created_at'], 'integer'],
            [['amountPay', 'amountGun', 'amountDiscounts', 'couponMoney'], 'number'],
            [['orderId'], 'string', 'max' => 60],
            [['paySn', 'orderSource', 'qrCode4PetroChina', 'gasId'], 'string', 'max' => 50],
            [['phone', 'litre'], 'string', 'max' => 20],
            [['orderTime', 'payTime', 'refundTime', 'couponCode'], 'string', 'max' => 30],
            [['gasName'], 'string', 'max' => 150],
            [['province', 'city', 'county', 'oilNo', 'orderStatusName', 'priceUnit', 'priceOfficial', 'priceGun'], 'string', 'max' => 15],
            [['payType'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增ID',
            'status' => '状态',
            'from' => '渠道',
            'orderId' => '订单号',
            'paySn' => '支付号',
            'phone' => '手机号',
            'orderTime' => '生成时间',
            'payTime' => '支付时间',
            'refundTime' => '退款时间',
            'gasName' => '油站名称',
            'province' => '省名称',
            'city' => '市名称',
            'county' => '县名称',
            'gunNo' => '枪号',
            'oilNo' => '油号',
            'amountPay' => '实付',
            'amountGun' => '总金额',
            'amountDiscounts' => '优惠金额',
            'orderStatusName' => '订单状态',
            'couponMoney' => '优惠券金额',
            'couponId' => '优惠券编号',
            'couponCode' => '优惠券Code',
            'litre' => '升数',
            'payType' => '支付方式',
            'priceUnit' => '最终单价',
            'priceOfficial' => '国标价',
            'priceGun' => '枪价',
            'orderSource' => '渠道编码',
            'qrCode4PetroChina' => '渠道编码',
            'gasId' => '油站ID',
            'created_at' => '同步时间',
        ];
    }
}
