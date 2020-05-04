<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;
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
    public function getListByLocals($lat, $lng)
    {
        $data = OilStations::find()
            ->select('gasId,gasAddressLongitude,gasAddressLatitude')
            ->where(['status' => StatusEnum::ENABLED])
            // ->andWhere(['in', 'location', $locals])
            // ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->orderBy('id desc')
            ->cache(60)
            ->asArray()
            ->all();

        $dataByLocal = [];
        foreach ($data as $datum) {
            $datum['distance'] = $this->getDistance($lat, $lng, $datum['gasAddressLatitude'], $datum['gasAddressLongitude']);
            $dataByLocal[] = $datum;
        }

        ArrayHelper::multisort($dataByLocal,'distance',SORT_ASC);
        return $dataByLocal;
    }

    //计算经纬度距离  km单位
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367; //地区半径6367km
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude      = $lng2 - $lng1;
        $calcLatitude       = $lat2 - $lat1;
        $stepOne            = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo            = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance, 1);
    }
}