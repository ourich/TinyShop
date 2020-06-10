<?php

namespace addons\TinyShop\frontend\controllers;

use Yii;
use common\controllers\AddonsController;
use addons\TinyShop\common\models\gas\GasStations;
use addons\TinyShop\common\models\gas\GasOrder;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;

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
        //测试入口
        $mobile = '13098878085';
        $longitude = '114.431413';
        $latitude = '30.407874';
        $response = Yii::$app->tinyShopService->member->countChilds(2);
        p($response);
        die();




        return $this->render('index',[

        ]);
    }

    /**
     * 同步车主邦订单
     * @return [type] [description]
     */
    public function actionOrder()
    {
        $response = Yii::$app->tinyShopService->czb->platformOrderInfoV2();
        // p($response);
        // die();
        
        if ($response['code'] == 200) {
            $result = $response['result'];
            foreach ($result as $value) {
                if (GasOrder::find()->where(['orderId' => $value['orderId']])->one()) {
                    continue; //如果存在，则跳过
                }
                $data[] = [
                  'orderId' => $value['orderId'],
                  'paySn' => $value['paySn'],
                  'phone' => $value['phone'],
                  'orderTime' => $value['orderTime'],
                  'payTime' => $value['payTime'],
                  'refundTime' => $value['refundTime'],
                  'gasName' => $value['gasName'],
                  'province' => $value['province'],
                  'city' => $value['city'],
                  'county' => $value['county'],
                  'gunNo' => $value['gunNo'],
                  'oilNo' => $value['oilNo'],
                  'amountPay' => $value['amountPay'],
                  'amountGun' => $value['amountGun'],
                  'amountDiscounts' => $value['amountDiscounts'],
                  'orderStatusName' => $value['orderStatusName'],
                  'couponMoney' => $value['couponMoney'],
                  'couponId' => $value['couponId'],
                  'couponCode' => $value['couponCode'],
                  'litre' => $value['litre'],
                  'payType' => $value['payType'],
                  'priceUnit' => $value['priceUnit'],
                  'priceOfficial' => $value['priceOfficial'],
                  'priceGun' => $value['priceGun'],
                  'orderSource' => $value['orderSource'],
                  'qrCode4PetroChina' => $value['qrCode4PetroChina'],
                  'gasId' => $value['gasId'],
                  'created_at' => time(),
                ];
            }
            //再执行批量插入
            if (isset($data)) 
            {
              Yii::$app->db->createCommand()
                   ->batchInsert(GasOrder::tableName(),['orderId','paySn','phone','orderTime','payTime','refundTime','gasName','province','city','county','gunNo','oilNo','amountPay','amountGun','amountDiscounts','orderStatusName','couponMoney','couponId','couponCode','litre','payType','priceUnit','priceOfficial','priceGun','orderSource','qrCode4PetroChina','gasId','created_at'],
                   $data)
                   ->execute();

              //更新会员优惠金【暂不开启】
              foreach ($data as $value) {
                // Yii::$app->tinyShopService->order->jiChaOil($value);
              }
            }
        }

        die('OK');
    }

    /**
     * 更新加油站列表
     * @return [type] [description]
     */
    public function actionUpstations()
    {
        $response = Yii::$app->tinyShopService->czb->queryGasInfoListOilNoNew();
        if ($response['code'] != 200) {
            return;
        }
        $result = $response['result'];
        foreach($result as $v)
        {
          if (GasStations::find()->where(['gasId' => $v['gasId']])->one()) {
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
            'channelId' => 0,
            'status' => 1,
          ];
        }
        //再执行批量插入
        if (isset($data)) 
        {
          Yii::$app->db->createCommand()
               ->batchInsert(GasStations::tableName(),['gasId','gasName','gasType','gasLogoBig','gasLogoSmall','gasAddress','gasAddressLongitude','gasAddressLatitude','provinceCode','cityCode','countyCode','provinceName','cityName','countyName','isInvoice','companyId','created_at','channelId','status'],
               $data)
               ->execute();
        }
        die('OK');
    }
}