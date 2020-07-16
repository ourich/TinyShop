<?php

namespace addons\TinyShop\services\xiaoju;

use Yii;
use common\components\Service;
use addons\TinyShop\services\xiaoju\xiaojuHeader;
use addons\TinyShop\common\models\common\OilStations;

/**
 * 车主邦接口
 * $response = Yii::$app->oil->queryGasInfoListOilNoNew();
 * $response = Yii::$app->oil->platformOrderInfoV2($parsers);
 * $response = Yii::$app->oil->queryPriceByPhone('ZH866640915,BF234895679', '13098878085');
 * 
 */
class xiaojuService extends Service
{
    protected $config;
    protected $gas;

    public function init()
    {
        // $defaultConfig = Yii::$app->debris->backendConfigAll();
        $this->config = [
            'base_uri' => 'https://test-mcs.czb365.com/services/v3/',
            'base_uri' => 'https://mcs.czb365.com/services/v3/',
            'apiKey' => 'mingxingshangpin1.0',
            'secret' => '3de902de6887b5838cd1abfce62377cd',
            'channelId' => '92652519',
        ];
        parent::init();
        // $this->gas = new Gas($this->config['apiKey'], $this->config['secret'], function() {
        //     return new Client([
        //         "base_uri" => $this->config['base_uri'],//可省略
        //     ]);
        // });
    }

    public function test()
    {
        $header = new xiaojuHeader;
        $queryData = array('pageIndex' => 1, 'pageSize' => 100);
        // $info = curl_xiaoJu('queryStoreList ', $queryData);
        $info = $header->curl_xiaoJu('queryStoreList ', $queryData);
        p($info);
    }

    /**
     * 全量油站数据
     * @return [type] [description]
     */
    public function getStoreList()
    {
        $header = new xiaojuHeader;
        $total = 0;
        $pageIndex = 1;
        // 用已经获取的条数和 totalSize 比较，判断是否结束查询；该接口每天请求一次后，将数据缓存至接入方本地即可 
        do {
            $queryData = array('pageIndex' => $pageIndex, 'pageSize' => 10);
            $info = $header->curl_xiaoJu('queryStoreList ', $queryData);
            if($info['code']!=0){
                exit($info['msg']);
            }
            $result = $info['data']['storeInfoList'];
            $totalSize = $info['data']['totalSize'];
            $total += count($info['data']['storeInfoList']);
            $pageIndex += 1;
            //入库前过滤
            foreach ($result as $v) {
                if (OilStations::find()->where(['gasId' => $v['storeId']])->one()) {
                    continue; //如果存在，则跳过
                }
                $data[] = [
                  'gasId' => $v['storeId'],
                  'gasName' => $v['storeName'],
                  // 'gasType' => $v['gasType'],
                  'gasLogoSmall' => $v['storeLogo'],
                  // 'gasLogoSmall' => $v['gasLogoSmall'],
                  'gasAddress' => $v['address'],    //小桔
                  'gasAddressLongitude' => $v['lon'],    //小桔
                  'gasAddressLatitude' => $v['lat'],    //小桔
                  // 'provinceCode' => $v['provinceCode'],
                  // 'cityCode' => $v['cityCode'],
                  // 'countyCode' => $v['countyCode'],
                  'provinceName' => $v['provinceName'],    //小桔
                  'cityName' => $v['cityName'], //小桔
                  // 'countyName' => $v['countyName'],
                  'isInvoice' => $v['invoiceManner'],
                  // 'companyId' => $v['companyId'],
                  'created_at' => time(),
                  'channelId' => 1,
                  'status' => 1,
                ];
            }
            //再执行批量插入
            if (isset($data)) 
            {
              Yii::$app->db->createCommand()
                   ->batchInsert(OilStations::tableName(),['gasId','gasName','gasLogoSmall','gasAddress','gasAddressLongitude','gasAddressLatitude','provinceName','cityName','isInvoice','created_at','channelId','status'],
                   $data)
                   ->execute();
            }
            unset($data);
            p($pageIndex);
        } while ($total < $totalSize);
        
        die();
    }

    /**
     * 根据用户查询油站状态和油价
     * @param  string $gasIds [description]
     * @param  string $phone  [description]
     * @return [type]         [description]
     */
    public function queryStorePrice($storeIdList = [], $mobile = '', $lon = '', $lat = '', $itemName = '92#', $openChannel = 1)
    {
        $header = new xiaojuHeader;
        $queryData = [
            'lon' => $lon,
            'lat' => $lat,
            'mobile' => $mobile,
            'openChannel' => 1,
            'itemName' => $itemName,
            'storeIdList' => $storeIdList,  //数组
        ];
        $info = $header->curl_xiaoJu('queryStorePrice ', $queryData);
        // p($info);
        // die();
    }

    /**
     * 详情页地址
     * @param  array   $storeIdList [description]
     * @param  string  $mobile      [description]
     * @param  string  $lon         [description]
     * @param  string  $lat         [description]
     * @param  string  $itemName    [description]
     * @param  integer $openChannel [description]
     * @return [type]               [description]
     */
    public function queryEnergyUrl($storeId = '', $mobile = '', $lon = '', $lat = '', $itemName = '92#', $openChannel = 1, $outUserId = '123456')
    {
        $header = new xiaojuHeader;
        $queryData = [
            'lon' => $lon,
            'lat' => $lat,
            'mobile' => $mobile,
            'openChannel' => 1,
            'itemName' => $itemName,
            'outUserId' => $outUserId,  //第三方平台UserId
            'storeId' => $storeId,  //单个ID
        ];
        $info = $header->curl_xiaoJu('queryEnergyUrl ', $queryData);
        p($info);
        die();
    }
    /**
     * 平台授权登录
     *
     * @param int $platformType 渠道编码，对接时车主邦提供
     * @param string $platformCode 平台用户唯一标识(手机号)
     * @return ApiResponse
     */
    public function notifyCheckUserInfo($data = array())
    {
        return $this->gas->login($this->config['channelId'], $platformCode)->result();
    }

    
    

}