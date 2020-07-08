<?php

namespace addons\TinyShop\frontend\controllers;

use Yii;
use common\controllers\AddonsController;
use common\enums\StatusEnum;
use yii\rest\Serializer;
use common\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use addons\TinyShop\common\models\common\OilStations;
use addons\TinyShop\common\models\common\OilOrder;
use common\models\member\Member;
use common\helpers\AddonHelper;

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
        $mobile = 'wap_user_1_13924041319';
        $gasId = 'ZG000003987';
        $lon = '114.431413';
        $lat = '30.407874';
        $order = Yii::$app->tinyShopService->order->findById(7); 
        $response = Yii::$app->tinyShopService->order->fanxian($order); 
        
        // $code_province = Yii::$app->services->provinces->getCode($response['province']);
        // $member = Member::findone(1);
        // Yii::warning($num_card);
        // p($response);
        die('hello');
    }

    public function actionMove()
    {
        $count = Yii::$app->tinyShopService->member->creatByOld();
        die($count);
    }

    public function actionOrder()
    {
        $response = Yii::$app->tinyShopService->czb->platformOrderInfoV2();
        // p($response);
        // die();
        
        if ($response['code'] == 200) {
            $result = $response['result'];
            foreach ($result as $value) {
                if (OilOrder::find()->where(['orderId' => $value['orderId']])->one()) {
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
                   ->batchInsert(OilOrder::tableName(),['orderId','paySn','phone','orderTime','payTime','refundTime','gasName','province','city','county','gunNo','oilNo','amountPay','amountGun','amountDiscounts','orderStatusName','couponMoney','couponId','couponCode','litre','payType','priceUnit','priceOfficial','priceGun','orderSource','qrCode4PetroChina','gasId','created_at'],
                   $data)
                   ->execute();

              //更新会员优惠金【暂不开启】
              foreach ($data as $value) {
                Yii::$app->tinyShopService->order->jiChaOil($value);
              }
            }
        }

        die('OK');
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
            'channelId' => 0,
            'status' => 1,
          ];
          $count += 1;
          // if ($count >= 10) {
          //     break;
          // }
        }

        //再执行批量插入
        if (isset($data)) 
        {
          Yii::$app->db->createCommand()
               ->batchInsert(OilStations::tableName(),['gasId','gasName','gasType','gasLogoBig','gasLogoSmall','gasAddress','gasAddressLongitude','gasAddressLatitude','provinceCode','cityCode','countyCode','provinceName','cityName','countyName','isInvoice','companyId','created_at','channelId','status'],
               $data)
               ->execute();
        }

        die();
        // return $this->render('index');
    }
}