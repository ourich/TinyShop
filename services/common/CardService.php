<?php

namespace addons\TinyShop\services\common;

use Yii;
use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use addons\TinyShop\common\enums\AdvLocalEnum;
use addons\TinyShop\common\models\common\OilCard;

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
            ];
        }

        $field = ['cardNo', 'code'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(OilCard::tableName(), $field, $rows)->execute();
    }
}