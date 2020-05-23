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
use common\enums\AgentEnum;

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

    /**
     * 区域代理奖金
     * @param  [type] $member_id [description]
     * @param  [type] $lon       [description]
     * @param  [type] $lat       [description]
     * @return [type]            [description]
     */
    public function areaSend($member_id, $lon, $lat)
    {
        $member = Member::findone($member_id);
        if ($member->area_send > 0) {
            return;
        }
        // Yii::$app->debris->p($member);
        // die();
        $area = Yii::$app->tinyShopService->area->getCityByLongLat($lon, $lat); 
        $code_district = Yii::$app->services->provinces->getCode($area['district']);
        //获取区代
        $area_agent = Member::find()
            ->where(['area_agent' => $code_district, 'is_agent' => AgentEnum::AREA])
            ->one();
        if ($area_agent) {
            Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                'member' => $area_agent,
                'pay_type' => 100,
                'num' => 0.05,
                'credit_group' => 'manager',
                'remark' => "区代激活奖励",
                'map_id' => 0,
            ]));
        }
        $code_city = Yii::$app->services->provinces->getCode($area['city']);
        $city_agent = Member::find()
            ->where(['city_agent' => $code_city, 'is_agent' => AgentEnum::CITY])
            ->one();
        if ($city_agent) {
            Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                'member' => $city_agent,
                'pay_type' => 100,
                'num' => 0.03,
                'credit_group' => 'manager',
                'remark' => "市代激活奖励",
                'map_id' => 0,
            ]));
        }
        $code_province = Yii::$app->services->provinces->getCode($area['province']);
        $province_agent = Member::find()
            ->where(['province_agent' => $code_province, 'is_agent' => AgentEnum::PROVINCE])
            ->one();
        if ($province_agent) {
            Yii::$app->services->memberCreditsLog->incrMoney(new CreditsLogForm([
                'member' => $province_agent,
                'pay_type' => 100,
                'num' => 0.02,
                'credit_group' => 'manager',
                'remark' => "省代激活奖励",
                'map_id' => 0,
            ]));
        }
        //更新奖励发放状态
        $member->area_send = 1;
        $member->save();
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
            // if ($count >= 2) {
            //     break;
            // }
        }
        $field = ['old_id', 'agentid', 'mobile', 'current_level', 'nickname', 'password_hash', 'credit1', 'credit2', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(Member::tableName(), $field, $rows)->execute();

        echo $count;

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
            //分配卡片
            $mobile = 'wap_user_1_'.$row['mobile'];
            $sql = "SELECT COUNT(*) FROM `ims_ewei_shop_adv` WHERE `openid`='".$mobile."' and enabled = 0 ";
            $cards_num=Yii::$app->db->createCommand($sql)->queryScalar(); 
            if ($cards_num > 0) {
                Yii::$app->tinyShopService->card->createFor($model->id, $cards_num);
            }

            
            //根据agentid找到老会员ID
            if ($model->agentid > 0) {
                $agent = $this->findByOldID($model->agentid);
                $model->pid = $agent->id;
                $model->save();
            }
            
        }
        
    }
}