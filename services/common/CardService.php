<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\models\common\OilCard;
use common\models\forms\CreditsLogForm;
use common\helpers\ArrayHelper;

/**
 * Class CardService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class CardService extends Service
{
    /**
     * 创建卡片
     *
     * @param CouponTypeForm $couponType
     * @param $count
     * @throws \yii\db\Exception
     */
    public function create($model)
    {
        $rows = [];
        $min = $model->cardNo;
        $max = $min + $model->giveNum;
        for ($i = $min; $i < $max; $i++) {
            $code = StringHelper::random(6);
            $rows[] = [
                'cardNo' => $i,
                'member_id' => $model->member_id,
                'code' => $code,
                'status' => 1,
            ];
        }

        $field = ['cardNo', 'member_id', 'code', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(OilCard::tableName(), $field, $rows)->execute();
    }

    public function createFor($member_id, $giveNum)
    {
        $rows = [];
        $min = OilCard::find()->max('cardNo');   //目前卡号最大值
        $min += 1;   //分配起始卡号
        $max = $min + $giveNum;
        for ($i = $min; $i < $max; $i++) {
            $code = StringHelper::random(6);
            $rows[] = [
                'cardNo' => $i,
                'member_id' => $member_id,
                'code' => $code,
                'status' => 1,
            ];
        }

        $field = ['cardNo', 'member_id', 'code', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(OilCard::tableName(), $field, $rows)->execute();
    }

    /**
     * 分配卡片
     *
     * @param CouponTypeForm $couponType
     * @param $count
     * @throws \yii\db\Exception
     */
    public function give($to, $min, $max)
    {
        return Yii::$app->db->createCommand()->update(OilCard::tableName(), ['member_id' => $to], "cardNo >= {$min} and cardNo <= {$max}")->execute();
    }

    /**
     * 虚拟卡转移
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function changeByOrder($from, $to, $num)
    {
        //先从V5的卡库里取出X张卡【未印刷的】
        $cards =  OilCard::find()
            ->where(['member_id' => $from])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['print' => StatusEnum::DISABLED])
            ->orderBy('id desc')
            ->asArray()
            ->limit($num)
            ->all();
        //把这些卡分配给购买人
        $cardids = ArrayHelper::getColumn($cards, 'id');
        $ids=implode(',',$cardids);
        
        Yii::$app->db->createCommand()->update(OilCard::tableName(), ['member_id' => $to], "id in ($ids)")->execute();
    }

    /**
     * 检查电子卡库存
     * @param  [type] $from [description]
     * @param  [type] $to   [description]
     * @param  [type] $num  [description]
     * @return [type]       [description]
     */
    public function countCard($member_id)
    {
        //先从V5的卡库里取出X张卡【未印刷的】
      return  $cards =  OilCard::find()
            ->where(['member_id' => $member_id])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->andWhere(['print' => StatusEnum::DISABLED])
            ->count();
    }

    /**
     * 根据推广码查询
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByPromoCode($promo_code)
    {
        return OilCard::find()
            ->where(['code' => $promo_code])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->one();
    }

    /**
     * 注册人获得卡余额，更新卡片信息
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function useCard($mobile, $promo_code)
    {
        $member = Yii::$app->services->member->findByMobile($mobile);
        $model = OilCard::find()
            ->where(['code' => $promo_code])
            ->andWhere(['status' => StatusEnum::ENABLED])
            ->one();
        $model->status = StatusEnum::DISABLED;
        $model->user = $member['id'];
        $model->end_at = time();
        if ($model->save()) {
            // 充值
            Yii::$app->services->memberCreditsLog->incrInt(new CreditsLogForm([
                'member' => $member,
                'pay_type' => 100,
                'num' => 100,
                'credit_group' => 'manager',
                'remark' => "激活卡片",
                'map_id' => 0,
            ]));
        }
    }
}