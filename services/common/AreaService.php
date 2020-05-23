<?php

namespace addons\TinyShop\services\common;

use common\components\Service;
use addons\TinyShop\common\models\common\Nice;

/**
 * Class NiceService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class AreaService extends Service
{

    /**
     * @param $topic_id
     * @param $topic_type
     * @param $member_id
     * @return Nice|array|\yii\db\ActiveRecord|null
     */
    public function findByTopicId($topic_id, $topic_type, $member_id)
    {
        $model = Nice::find()
            ->where([
                'topic_id' => $topic_id,
                'topic_type' => $topic_type,
                'member_id' => $member_id,
            ])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();

        if (!$model) {
            $model = new Nice();
        }

        return $model;
    }

    /**
     * 根据经纬度获得省市区
     * @param  [type] $lon [description]
     * @param  [type] $lat [description]
     * @return [type]      [description]
     */
    public function getCityByLongLat($lon, $lat) {
        if ($lon == '' || $lat == '') return '';
        $url = "http://api.map.baidu.com/geocoder?location={$lat},{$lon}&coordtype=wgs84ll";
        // $url = "http://api.map.baidu.com/geocoder/v2/?ak=wWYw0yCb8ntXmSgTxTx40vKR&location={$lon},{$lat}&output=json&coordtype=bd09ll";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $data = json_decode(json_encode(simplexml_load_string($output, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data['result']['addressComponent'];
    }
}