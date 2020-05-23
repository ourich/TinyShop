<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%oil_delivery}}".
 *
 * @property int $id 序号
 * @property int $status 状态
 * @property int $type 类型
 * @property int $member_id 持有人
 * @property int $cardNo 起始卡号
 * @property int $cardNum 数量
 * @property string $name 姓名
 * @property int $mobile 电话
 * @property string $address 收货地址
 * @property int $created_at 创建时间
 */
class OilDelivery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oil_delivery}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'member_id', 'cardNo', 'cardNum', 'mobile', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['reply'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'status' => '状态',
            'type' => '类型',
            'member_id' => '持有人',
            'cardNo' => '起始卡号',
            'cardNum' => '数量',
            'reply' => '快递单号',
            'name' => '姓名',
            'mobile' => '电话',
            'address' => '收货地址',
            'created_at' => '创建时间',
        ];
    }
}
