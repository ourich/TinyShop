<?php

namespace addons\TinyShop\common\models\commission;

use Yii;

/**
 * This is the model class for table "{{%commission_level}}".
 *
 * @property int $id 序号
 * @property string $name 等级名称
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $detail 会员介绍
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 * @property int $invit 直推人数
 * @property string $commission_shop 消费分润
 * @property string $commission_oil 加油分润
 */
class CommissionLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%commission_level}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at', 'invit'], 'integer'],
            [['commission_shop', 'commission_oil'], 'number'],
            [['name', 'detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'name' => '等级名称',
            'status' => '状态[-1:删除;0:禁用;1启用]',
            'detail' => '会员介绍',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'invit' => '直推人数',
            'commission_shop' => '消费分润',
            'commission_oil' => '加油分润',
        ];
    }
}
