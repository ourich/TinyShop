<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%oil_card}}".
 *
 * @property int $id 序号
 * @property int $status 状态
 * @property int $type 类型
 * @property int $member_id 持有人
 * @property int $user 使用者
 * @property int $cardNo 卡号
 * @property string $code 密码
 * @property int $created_at 创建时间
 * @property int $end_at 使用时间
 */
class OilCard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oil_card}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'member_id', 'user', 'cardNo', 'created_at', 'end_at'], 'integer'],
            [['code'], 'string', 'max' => 10],
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
            'user' => '使用者',
            'cardNo' => '卡号',
            'code' => '密码',
            'created_at' => '创建时间',
            'end_at' => '使用时间',
        ];
    }
}
