<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\models\common\OilCard;
use common\models\forms\CreditsLogForm;

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
    public function create($count)
    {
        $rows = [];
        $min = OilCard::find()->max('cardNo');   //目前卡号最大值
        $min = $min ?? '100000000';
        $min += 1;
        $max = $min + $count;
        for ($i = $min; $i < $max; $i++) {
            $code = StringHelper::random(6);
            $rows[] = [
                'cardNo' => $i,
                'code' => $code,
                'status' => 1,
            ];
        }

        $field = ['cardNo', 'code', 'status'];
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
     * 根据推广码查询
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findByPromoCode($promo_code)
    {
        return OilCard::find()
            ->where(['code' => $promo_code])
            ->one();
    }

    /**
     * 注册人获得卡余额，更新卡片信息
     *
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function useCard($user, $promo_code)
    {
        $model = OilCard::find()
            ->where(['code' => $promo_code])
            ->one();
        $model->status = StatusEnum::DISABLED;
        $model->user = $user;
        $model->end_at = time();
        if ($model->save()) {
            $member = Yii::$app->services->member->get($user);
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