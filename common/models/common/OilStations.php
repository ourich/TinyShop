<?php

namespace addons\TinyShop\common\models\common;

use Yii;

/**
 * This is the model class for table "{{%oil_stations}}".
 *
 * @property int $id 自增ID
 * @property string $gasId 油站 id
 * @property string $gasName 油站名称
 * @property int $gasType 油站类型:1 中石油，2 中石化，3 壳牌，4 其他
 * @property string $gasLogoBig 大 logo 图片链接
 * @property string $gasLogoSmall 小 logo 图片链接
 * @property string $gasAddress 加油站详情地址
 * @property string $gasAddressLongitude 油站经度
 * @property string $gasAddressLatitude 油站纬度
 * @property int $provinceCode 省编码
 * @property int $cityCode 市编码
 * @property int $countyCode 县编码
 * @property string $provinceName 省份
 * @property string $cityName 城市
 * @property string $countyName 区县
 * @property int $isInvoice 是否能开发票 0 不能开 1 能开
 * @property int $companyId 公司代码
 * @property int $created_at 创建时间
 * @property int $status 状态
 */
class OilStations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oil_stations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gasId', 'gasName', 'gasLogoBig', 'gasLogoSmall', 'gasAddress', 'gasAddressLongitude', 'gasAddressLatitude', 'provinceCode', 'cityCode', 'countyCode', 'isInvoice', 'companyId'], 'required'],
            [['gasType', 'provinceCode', 'cityCode', 'countyCode', 'isInvoice', 'companyId', 'created_at', 'status'], 'integer'],
            [['gasAddressLongitude', 'gasAddressLatitude'], 'number'],
            [['gasId'], 'string', 'max' => 30],
            [['gasName'], 'string', 'max' => 60],
            [['gasLogoBig', 'gasLogoSmall'], 'string', 'max' => 250],
            [['gasAddress'], 'string', 'max' => 150],
            [['provinceName', 'cityName', 'countyName'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gasId' => '油站 id',
            'gasName' => '油站名称',
            'gasType' => '油站类型',
            'gasLogoBig' => '大 logo 图片链接',
            'gasLogoSmall' => '小 logo 图片链接',
            'gasAddress' => '加油站详情地址',
            'gasAddressLongitude' => '油站经度',
            'gasAddressLatitude' => '油站纬度',
            'provinceCode' => '省编码',
            'cityCode' => '市编码',
            'countyCode' => '县编码',
            'provinceName' => '省份',
            'cityName' => '城市',
            'countyName' => '区县',
            'isInvoice' => '发票',
            'companyId' => '公司代码',
            'created_at' => '创建时间',
            'status' => '状态',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->created_at = time();

        return parent::beforeSave($insert);
    }
}
