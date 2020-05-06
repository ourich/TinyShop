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
        // $data = Yii::$app->tinyShopService->oil->getListByLocals('30.530587684990884', '114.31723779052734');
        $data_all = OilStations::find()
            ->select('gasId,gasAddressLongitude,gasAddressLatitude')
            ->where(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->cache(60)
            ->asArray()
            ->all();

        $dataByLocal = [];
        foreach ($data_all as $datum) {
            $datum['distance'] = $this->getDistance('39.9', '116.4', $datum['gasAddressLatitude'], $datum['gasAddressLongitude']);
            $dataByLocal[] = $datum;
        }
        //按距离排序
        ArrayHelper::multisort($dataByLocal,'distance',SORT_ASC);

        $data = new ArrayDataProvider([
            'allModels' => $dataByLocal,
            'pagination' => [
                'pageSize' => 5,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
        $models = (new Serializer())->serialize($data);
        $gasIds = ArrayHelper::getColumn($models, 'gasId');
        $gasIds=implode(',',$gasIds);
        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($gasIds, '13098878085');

        $results = $response['result'];
        foreach ($results as &$result) {
            $result = $this->regroupShow($result, '39.9', '116.4');
        }





        
        print_r($results);
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