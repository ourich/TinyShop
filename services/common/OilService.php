<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use common\enums\StatusEnum;
use addons\TinyShop\common\models\common\OilStations;

/**
 * Class AdvService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class OilService extends Service
{
    /**
     * 获取广告列表
     *
     * @param array $locals
     * @return array
     */
    public function getListByLocals(array $locals)
    {
        if (empty($locals)) {
            return $locals;
        }

        $data = OilStations::find()
            ->where(['status' => StatusEnum::ENABLED])
            // ->andWhere(['in', 'location', $locals])
            // ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->cache(60)
            ->asArray()
            ->all();

        // $dataByLocal = [];
        // foreach ($data as $datum) {
        //     $dataByLocal[$datum['location']][] = $datum;
        // }


        return $data;
    }
}