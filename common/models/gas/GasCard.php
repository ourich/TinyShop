<?php

namespace addons\TinyShop\common\models\gas;

use Yii;
use common\models\member\Member;

/**
 * This is the model class for table "{{%gas_card}}".
 *
 * @property int $id 序号
 * @property int $status 状态
 * @property int $type 类型
 * @property int $print 是否打印
 * @property int $member_id 持有人
 * @property int $user 使用者
 * @property int $cardNo 卡号
 * @property string $code 密码
 * @property string $img 二维码
 * @property int $created_at 创建时间
 * @property int $end_at 使用时间
 */
class GasCard extends \yii\db\ActiveRecord
{
    const STATE_UNUNSED = 0;
    const STATE_GET = 1;
    const STATE_UNSED = 2;
    const STATE_PAST_DUE = 3;

    /**
     * @var array
     */
    public static $stateExplain = [
        self::STATE_UNUNSED => '未领取',
        self::STATE_GET => '已领取',
        self::STATE_UNSED => '已使用',
        self::STATE_PAST_DUE => '已过期',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%gas_card}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'print', 'member_id', 'user', 'cardNo', 'created_at', 'end_at'], 'integer'],
            [['code'], 'string', 'max' => 10],
            [['img'], 'string', 'max' => 250],
        ];
    }
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id' => 'user'])->from(Member::tableName().' alias_member1');
        // return $this->hasOne(Store::className(), ['id' => 'store_id'])->from(Store::tableName().' alias_store1');
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
            'print' => '是否打印',
            'member_id' => '持有人',
            'user' => '使用者',
            'cardNo' => '卡号',
            'code' => '密码',
            'img' => '二维码',
            'created_at' => '创建时间',
            'end_at' => '使用时间',
        ];
    }
}
