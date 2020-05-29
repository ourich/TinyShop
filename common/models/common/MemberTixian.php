<?php

namespace addons\TinyShop\common\models\common;

use Yii;
use common\models\member\Member;

/**
 * This is the model class for table "{{%member_tixian}}".
 *
 * @property int $id 序号
 * @property int $status 状态
 * @property int $type 平台类型
 * @property int $member_id 用户
 * @property string $money 金额
 * @property string $fee 手续费
 * @property string $account 收款账户
 * @property string $account_img 收款码
 * @property string $name 开户姓名
 * @property string $bank_name 开户行
 * @property string $mobile 联系电话
 * @property string $remark 驳回原因
 * @property int $created_at 创建时间
 */
class MemberTixian extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%member_tixian}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'member_id', 'created_at'], 'integer'],
            [['money', 'fee'], 'number'],
            [['account', 'account_img', 'remark'], 'string', 'max' => 250],
            [['name'], 'string', 'max' => 10],
            [['bank_name'], 'string', 'max' => 60],
            [['mobile'], 'string', 'max' => 20],
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
            'type' => '平台类型',
            'member_id' => '用户',
            'money' => '金额',
            'fee' => '手续费',
            'account' => '收款账户',
            'account_img' => '收款码',
            'name' => '开户姓名',
            'bank_name' => '开户行',
            'mobile' => '联系电话',
            'remark' => '驳回原因',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = time();
            $this->member_id = Yii::$app->user->identity->member_id;
        }

        return parent::beforeSave($insert);
    }
}
