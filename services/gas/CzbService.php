<?php

namespace addons\TinyShop\services\gas;

use common\components\Service;
use GuzzleHttp\Client;
use JavaReact\CzbApi\Gas;
use JavaReact\CzbApi\Tools\GpsTransform;

/**
 * 车主邦接口
 * $response = Yii::$app->gas->queryGasInfoListOilNoNew();
 * $response = Yii::$app->gas->platformOrderInfoV2($parsers);
 * $response = Yii::$app->gas->queryPriceByPhone('ZH866640915,BF234895679', '13098878085');
 * 
 */
class CzbService extends Service
{
    protected $config;
    protected $gas;

    public function init()
    {
        // $defaultConfig = Yii::$app->debris->backendConfigAll();
        $this->config = [
            // 'base_uri' => 'https://test-mcs.czb365.com/services/v3/',
            'base_uri' => 'https://mcs.czb365.com/services/v3/',
            'apiKey' => 'mingxingshangpin1.0',
            'secret' => '3de902de6887b5838cd1abfce62377cd',
            'channelId' => '92652519',
        ];
        parent::init();
        $this->gas = new Gas($this->config['apiKey'], $this->config['secret'], function() {
            return new Client([
                "base_uri" => $this->config['base_uri'],//可省略
            ]);
        });
    }

    /**
     * 获取油站全量数据（下一步准备改为异步请求）
     * @return [type] [description]
     */
    public function queryGasInfoListOilNoNew()
    {
        return $this->gas->queryGasInfoListOilNoNew($this->config['channelId'])->result();
    }

    /**
     * 根据用户查询油站状态和油价
     * @param  string $gasIds [description]
     * @param  string $phone  [description]
     * @return [type]         [description]
     */
    public function queryPriceByPhone(string $gasIds, string $phone)
    {
        return $this->gas->queryPriceByPhone($gasIds, $this->config['channelId'], $phone)->result();
    }
    /**
     * 平台授权登录
     *
     * @param int $platformType 渠道编码，对接时车主邦提供
     * @param string $platformCode 平台用户唯一标识(手机号)
     * @return ApiResponse
     */
    public function login(string $platformCode)
    {
        return $this->gas->login($this->config['channelId'], $platformCode)->result();
    }

    /**
     * 查询订单
     * @param  string  $orderSource [description]
     * @param  integer $pageIndex   [description]
     * @param  integer $pageSize    [description]
     * @param  array   $extraParam  [description]
     * @return [type]               [description]
     */
    public function platformOrderInfoV2($extraParam = [], $pageIndex = 1, $pageSize = 100)
    {
        return $this->gas->platformOrderInfoV2($this->config['channelId'], $pageIndex, $pageSize, $extraParam)->result();
    }

    /**
     * 坐标系转换
     * @param string $lon [description]
     * @param string $lat [description]
     */
    public function WGS84toGCJ02(string $lon, string $lat)
    {
        $Trans = new GpsTransform();
        return $Trans->WGS84toGCJ02($lon, $lat);
    }
    

}