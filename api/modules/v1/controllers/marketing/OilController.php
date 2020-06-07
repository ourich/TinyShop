<?php

namespace addons\TinyShop\api\modules\v1\controllers\marketing;

use Yii;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\rest\Serializer;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use api\controllers\OnAuthController;
use yii\web\NotFoundHttpException;
use addons\TinyShop\common\models\common\OilStations;
use common\models\member\Member;

/**
 * 优惠券领取列表
 *
 * Class CouponTypeController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class OilController extends OnAuthController
{
    /**
     * @var CouponType
     */
    public $modelClass = OilStations::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view', 'Detail'];

    /**
     * @return mixed|ActiveDataProvider
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return ResultHelper::json(422, '请先登陆');
        }
        $who = Yii::$app->request->get();
        if (!Yii::$app->user->isGuest) {
            $member = Member::findone(Yii::$app->user->identity->member_id);
            $mobile = $member['mobile'];
        }
        // $mobile = '13098878085'; 
        //坐标系转换
        $zuobiao = Yii::$app->tinyShopService->czb->WGS84toGCJ02($who['longitude'], $who['latitude']);
        // return ResultHelper::json(422, $zuobiao);
        // 取出所有数据并缓存
        $fanwei = 5;
        $data_all = $this->modelClass::find()
            ->select('gasId,gasAddressLongitude,gasAddressLatitude')
            ->where(['status' => StatusEnum::ENABLED])
            ->andFilterWhere(['between','gasAddressLongitude', $zuobiao['lon'] - $fanwei, $zuobiao['lon'] + $fanwei])
            ->andFilterWhere(['between','gasAddressLatitude', $zuobiao['lat'] - $fanwei, $zuobiao['lat'] + $fanwei])
            ->orderBy('id desc')
            ->asArray()
            ->all();

        $dataByLocal = [];
        foreach ($data_all as $datum) {
            $datum['distance'] = $this->getDistance($zuobiao['lat'], $zuobiao['lon'], $datum['gasAddressLatitude'], $datum['gasAddressLongitude']);
            $dataByLocal[] = $datum;
        }
        //按距离排序
        ArrayHelper::multisort($dataByLocal,'distance',SORT_ASC);
        //只取10条
        $provider = new ArrayDataProvider([
            'allModels' => $dataByLocal,
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
        $counts = $provider->getCount();
        if ($counts == 0) {
            return ResultHelper::json(200, '到底了');
        }
        $data = $provider->getModels();
        // 主要生成header的page信息
        // $data = (new Serializer())->serialize($data);
        $gasIds = ArrayHelper::getColumn($data, 'gasId');
        $gasIds=implode(',',$gasIds);

        if ($member['oil_token_time'] < time()) {
            $areaSend = Yii::$app->tinyShopService->member->areaSend(Yii::$app->user->identity->member_id, $who['longitude'], $who['latitude']);
            $token = Yii::$app->tinyShopService->czb->login($mobile);
            $user = Member::findOne($member['id']);
            Member::updateAll(['oil_token'=>$token['result']['token'],'oil_token_time'=>time() + 21*24*3600],['id'=>$member['id']]);
        }
        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($gasIds, $mobile);
        $results = $response['result'];
        // return $results;
        foreach ($results as &$result) {
            $result = $this->regroupShow($result, $zuobiao['lat'], $zuobiao['lon'], $mobile);
        }
        ArrayHelper::multisort($results,'distance',SORT_ASC);
        return $results;
    }

    /**
     * 重组显示
     *
     * @param $model
     * @return mixed
     */
    public function regroupShow($model, $latitude, $longitude, $mobile)
    {
        // 是否可领取 
        $other = OilStations::find()->select('gasAddress,gasAddressLatitude,gasAddressLongitude,gasLogoSmall')->where(['gasId'=>$model['gasId']])->one();
        $model['gasName'] = mb_substr($model['gasName'], 0, 15, 'utf-8');
        $model['gasAddress'] = $other['gasAddress'];
        $model['gasAddressLongitude'] = $other['gasAddressLongitude'];
        $model['gasAddressLatitude'] = $other['gasAddressLatitude'];
        $model['gasLogoSmall'] = $other['gasLogoSmall'];
        $model['distance'] = $this->getDistance($latitude, $longitude, $model['gasAddressLatitude'], $model['gasAddressLongitude']);
        $model['oilPriceList'] = ArrayHelper::index($model['oilPriceList'], 'oilNo');
        $model['gunNos'] = ArrayHelper::getColumn($model['oilPriceList'], 'gunNos');
        $model['priceYfq'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceYfq');
        $model['priceOfficial'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceOfficial');
        $model['priceDiscount'] = number_format($model['priceOfficial'] - $model['priceYfq'], 2);
        $model['mobile'] = $mobile;
        $model['url'] = 'https://open.czb365.com/redirection/todo/?platformType=92652519&platformCode=' . $mobile . '&gasId=' . $model['gasId'] . '&gunNo=';

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
     * 取出所有数据并缓存
     * @return [type] [description]
     */
    public function getAlldata()
    {
        // 取出所有数据并缓存
        $data = $this->modelClass::find()
            ->select('gasId,gasAddressLongitude,gasAddressLatitude')
            ->where(['status' => StatusEnum::ENABLED])
            ->orderBy('id desc')
            ->cache(60)
            ->asArray()
            ->all();

        return $data;
    }

    /**
     * @param $id
     * @return mixed|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionDetail($id, $longitude, $latitude)
    {
        if (!Yii::$app->user->isGuest) {
            $member = Member::findone(Yii::$app->user->identity->member_id);
            $mobile = $member['mobile'];
        }
        // $mobile = '13098878085';
        // $id = 'YY998700654';
        // return ResultHelper::json(422, $id);
        //坐标系转换
        $zuobiao = Yii::$app->tinyShopService->czb->WGS84toGCJ02($longitude, $latitude);
        
        if ($member['oil_token_time'] < time()) {
            $token = Yii::$app->tinyShopService->czb->login($mobile);
            $user = Member::findOne($member['id']);
            Member::updateAll(['oil_token'=>$token['result']['token'],'oil_token_time'=>time() + 21*24*3600],['id'=>$member['id']]);
        }
        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($id, $mobile);
        if ($response['code'] != 200) {
            throw new NotFoundHttpException('请求的数据不存在');
        }
        $result = $response['result'];
        // Yii::$app->debris->p($result);
        return $this->regroupShow($result[0], $zuobiao['lat'], $zuobiao['lon'], $mobile);
    }

    /**
     * 权限验证
     *
     * @param string $action 当前的方法
     * @param null $model 当前的模型类
     * @param array $params $_GET变量
     * @throws \yii\web\BadRequestHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // 方法名称
        if (in_array($action, ['delete', 'update'])) {
            throw new \yii\web\BadRequestHttpException('权限不足');
        }
    }
}