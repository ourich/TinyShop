<?php

namespace addons\TinyShop\services\member;

use Yii;
use common\enums\StatusEnum;
use common\helpers\EchantsHelper;
use common\models\member\Member;
use common\models\member\Account;
use common\components\Service;
use common\helpers\ArrayHelper;
use common\models\forms\CreditsLogForm;

/**
 * Class MemberService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class MemberService extends Service
{
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id, $select = ['*'])
    {
        return Member::find()
            ->select($select)
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
    public function findByOldID($old_id)
    {
        return Member::find()
            ->where(['old_id' => $old_id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }
    public function creatByOld()
    {
        // 先获取所有老会员资料
        // 给他们创建新账户[id.agentid,mobile,agentlevel.nickname.credit1.credit2]
        // 绑定推荐关系agentid=old_id

        // 查询多条
        $sql = "SELECT `id`,`agentid`,`mobile`,`agentlevel`,`nickname`,`credit1`,`credit2` FROM `ims_ewei_shop_member`";
        $users=Yii::$app->db->createCommand($sql)->queryAll();
        $rows = [];
        $count = 0;
        foreach ($users as $user) {
            $rows[] = [
                'old_id' => $user['id'],
                'agentid' => $user['agentid'],
                'mobile' => $user['mobile'],
                'current_level' => $user['agentlevel'] +1,
                'nickname' => $user['nickname'],
                'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
                'credit1' => $user['credit1'],
                'credit2' => $user['credit2'],
                'status' => 1,
            ];
            $count ++ ;
            if ($count >= 2) {
                break;
            }
        }
        $field = ['old_id', 'agentid', 'mobile', 'current_level', 'nickname', 'password_hash', 'credit1', 'credit2', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Member::tableName(), $field, $rows)->execute();

        //全部执行完后，再更新资料，推荐人，余额，积分
        // $newusers = ArrayHelper::getColumn($rows, 'mobile');
        
        foreach ($rows as $row) {
            $model = Yii::$app->services->member->findByMobile($row['mobile']);
            //创建资产账户
            $account = new Account();
            $account->member_id = $model->id;
            $account->merchant_id = $model->merchant_id;
            $account->save();
            //更新余额积分
            if ($model->credit2 > 0) {
                Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                    'member' => $model,
                    'pay_type' => 100,
                    'num' => $model->credit2,
                    'credit_group' => 'manager',
                    'remark' => "迁移老系统余额",
                    'map_id' => 0,
                ]));
            }
            if ($model->credit1 > 0) {
                Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
                    'member' => $model,
                    'pay_type' => 100,
                    'num' => $model->credit1,
                    'credit_group' => 'manager',
                    'remark' => "迁移老系统优惠金",
                    'map_id' => 0,
                ]));
            }

            
            //根据agentid找到老会员ID
            if ($model->agentid > 0) {
                $agent = $this->findByOldID($model->agentid);
                $model->pid = $agent->id;
                $model->save();
            }
            
        }

        return $count;
    }
}