<?php

namespace addons\TinyShop\frontend\controllers;

use Yii;
use common\controllers\AddonsController;
use common\enums\StatusEnum;
use yii\rest\Serializer;
use common\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use addons\TinyShop\common\models\common\OilStations;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\TinyShop\frontend\controllers
 */
class DefaultController extends BaseController
{
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        print_r('<pre>');
        $mobile = '13098878085';
        $gasId = 'JY000011413';

        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($gasId, $mobile);
        // https://test-open.czb365.com/redirection/todo/?platformType=92652519&platformCode=13098878085&gasId=JY000011413&gunNo=1
        




        
        print_r($response);
        die();
        // return $this->render('index',[]);
    }
    /**
     * 重组显示
     *
     * @param $model
     * @return mixed
     */
    public function regroupShow($model, $latitude, $longitude)
    {
        // 是否可领取 
        $other = OilStations::find()->select('gasAddress,gasAddressLatitude,gasAddressLongitude')->where(['gasId'=>$model['gasId']])->one();
        $model['gasAddress'] = $other['gasAddress'];
        $model['gasAddressLongitude'] = $other['gasAddressLongitude'];
        $model['gasAddressLatitude'] = $other['gasAddressLatitude'];
        $model['distance'] = $this->getDistance($latitude, $longitude, $model['gasAddressLatitude'], $model['gasAddressLongitude']);
        $model['oilPriceList'] = ArrayHelper::index($model['oilPriceList'], 'oilNo');
        $model['priceYfq'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceYfq');
        $model['priceOfficial'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceOfficial');
        $model['priceDiscount'] = $model['priceOfficial'] - $model['priceYfq'];

        return $model;
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

    /**
     * 更新油站数据
     * @return [type] [description]
     */
    public function actionUp()
    {
        $model = new OilStations();
        $response = Yii::$app->tinyShopService->czb->queryGasInfoListOilNoNew();
        $result = $response['result'];

        //批量更新,并将需要批量插入的数据放入数组中 
        $count = 0;
        foreach($result as $v)
        {
          if ($model->find()->where(['gasId' => $v['gasId']])->one()) {
              continue; //如果存在，则跳过
          }

          $data[] = [
            'gasId' => $v['gasId'],
            'gasName' => $v['gasName'],
            'gasType' => $v['gasType'],
            'gasLogoBig' => $v['gasLogoBig'],
            'gasLogoSmall' => $v['gasLogoSmall'],
            'gasAddress' => $v['gasAddress'],
            'gasAddressLongitude' => $v['gasAddressLongitude'],
            'gasAddressLatitude' => $v['gasAddressLatitude'],
            'provinceCode' => $v['provinceCode'],
            'cityCode' => $v['cityCode'],
            'countyCode' => $v['countyCode'],
            'provinceName' => $v['provinceName'],
            'cityName' => $v['cityName'],
            'countyName' => $v['countyName'],
            'isInvoice' => $v['isInvoice'],
            'companyId' => $v['companyId'],
            'created_at' => time(),
            'status' => 1,
          ];
          $count += 1;
          if ($count >= 5) {
              break;
          }
        }

        //再执行批量插入
        if (isset($data)) 
        {
          Yii::$app->db->createCommand()
               ->batchInsert(OilStations::tableName(),['gasId','gasName','gasType','gasLogoBig','gasLogoSmall','gasAddress','gasAddressLongitude','gasAddressLatitude','provinceCode','cityCode','countyCode','provinceName','cityName','countyName','isInvoice','companyId','created_at','status'],
               $data)
               ->execute();
        }

        echo $count;
        die();
        // return $this->render('index');
    }
}