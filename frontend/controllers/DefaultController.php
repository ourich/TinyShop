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
    public function actionTest()
    {
        print_r('<pre>');
        $mobile = '13098878085';
        $gasId = 'JY000011413';

        $model = new OilStations();
        $response = $model->find()->where(['gasId' => $gasId])->one();




        echo "------";
        print_r($response);
        die();
        // return $this->render('index',[]);
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