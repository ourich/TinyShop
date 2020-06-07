<?php

namespace addons\TinyShop\api\modules\v1\controllers\gas;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\rest\Serializer;
use common\enums\StatusEnum;
use common\enums\WhetherEnum;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use addons\TinyShop\common\models\gas\GasStations;
use api\controllers\OnAuthController;
use yii\web\NotFoundHttpException;
use common\models\member\Member;

/**
 * 优惠券领取列表
 *
 * Class StationController
 * @package addons\TinyShop\api\controllers
 * @author jianyan74 <751393839@qq.com>
 */
class StationController extends OnAuthController
{
    /**
     * @var GasStations
     */
    public $modelClass = GasStations::class;

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['index', 'view'];

    /**
     * @return mixed|ActiveDataProvider
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return ResultHelper::json(422, '请先登陆');
        }
        $member = Yii::$app->tinyShopService->member->findById(Yii::$app->user->identity->member_id);
        if ($member['oil_token_time'] < time()) {
            // $areaSend = Yii::$app->tinyShopService->member->areaSend(Yii::$app->user->identity->member_id, $who['longitude'], $who['latitude']);
            $token = Yii::$app->tinyShopService->czb->login($member['mobile']);
            Member::updateAll(['oil_token'=>$token['result']['token'],'oil_token_time'=>time() + 21*24*3600],['id'=>$member['id']]);
        }

        $longitude = Yii::$app->request->get('longitude');
        $latitude = Yii::$app->request->get('latitude');
        $local = Yii::$app->tinyShopService->czb->WGS84toGCJ02($longitude, $latitude);  //坐标转换
        $slat = $local['lon'];
        $slng = $local['lat'];
        $fanwei = 1;    //缩小范围 
        $data = new ActiveDataProvider([
            'query' => GasStations::find()
                ->select('*, acos(
                  sin(('.$slng.'*3.1415)/180) * sin((gasAddressLatitude*3.1415)/180) + 
                  cos(('.$slng.'*3.1415)/180) * cos((gasAddressLatitude*3.1415)/180) * cos(('.$slat.'*3.1415)/180 - (gasAddressLongitude*3.1415)/180)
                  )*6370.996 AS juli')
                ->where([
                    'status' => StatusEnum::ENABLED,
                ])
                ->andFilterWhere(['between','gasAddressLongitude', $local['lon'] - $fanwei, $local['lon'] + $fanwei])
                ->andFilterWhere(['between','gasAddressLatitude', $local['lat'] - $fanwei, $local['lat'] + $fanwei])
                ->orderBy('juli asc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);

        // 主要生成header的page信息
        $models = (new Serializer())->serialize($data);
        $stations = ArrayHelper::index($models, 'gasId');   //油站原始数据

        $gasIds = ArrayHelper::getColumn($models, 'gasId');
        $gasIds=implode(',',$gasIds);
        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($gasIds, $member['mobile']);
        $results = $response['result'];
        foreach ($results as &$result) {
            $result = $this->regroupShow($result, $stations[$result['gasId']], $member['mobile']);
        }
        ArrayHelper::multisort($results,'juli',SORT_ASC);

        return $results;
    }

    /**
     * @param $id
     * @return mixed|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionDetail($id, $longitude, $latitude)
    {
        if (Yii::$app->user->isGuest) {
            return ResultHelper::json(422, '请先登陆');
        }
        $member = Yii::$app->tinyShopService->member->findById(Yii::$app->user->identity->member_id);
        $local = Yii::$app->tinyShopService->czb->WGS84toGCJ02($longitude, $latitude);  //坐标转换
        
        $response = Yii::$app->tinyShopService->czb->queryPriceByPhone($id, $member['mobile']);
        if ($response['code'] != 200) {
            throw new NotFoundHttpException('请求的数据不存在');
        }
        $result = $response['result'];
        $station = GasStations::find()->select('gasAddress,gasAddressLatitude,gasAddressLongitude,gasLogoSmall')->where(['gasId'=>$id])->one();
        $juli = $this->getDistance($local['lat'], $local['lon'], $station['gasAddressLatitude'], $station['gasAddressLongitude']);
        $stations = ArrayHelper::merge(['juli' => $juli], $station);
        return $this->regroupShow($result[0], $stations, $member['mobile']);
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
        return $calculatedDistance;
    }

    /**
     * 重组显示
     *
     * @param $model
     * @return mixed
     */
    public function regroupShow($model, $stations, $mobile)
    {
        $model['gasName'] = mb_substr($model['gasName'], 0, 15, 'utf-8');
        $model['gasAddress'] = $stations['gasAddress'];
        $model['gasAddressLongitude'] = $stations['gasAddressLongitude'];
        $model['gasAddressLatitude'] = $stations['gasAddressLatitude'];
        $model['gasLogoSmall'] = $stations['gasLogoSmall'];
        $model['juli'] = round($stations['juli'], 1);
        $model['oilPriceList'] = ArrayHelper::index($model['oilPriceList'], 'oilNo');
        $model['gunNos'] = ArrayHelper::getColumn($model['oilPriceList'], 'gunNos');
        $model['priceYfq'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceYfq');
        $model['priceOfficial'] = ArrayHelper::getValue($model['oilPriceList'], '92.priceOfficial');
        $model['priceDiscount'] = number_format($model['priceOfficial'] - $model['priceYfq'], 2);
        $model['url'] = 'https://open.czb365.com/redirection/todo/?platformType=92652519&platformCode=' . $mobile . '&gasId=' . $model['gasId'] . '&gunNo=';

        return $model;
    }

    /**
     * @return mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        $data = Yii::$app->request->post();
        $model = new CouponTypeForm();
        $model->attributes = $data;
        $model->member_id = Yii::$app->user->identity->member_id;
        if (!$model->validate()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        // 事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = Yii::$app->tinyShopService->marketingCoupon->give($model->couponType, $model->member_id);
            $transaction->commit();

            return ResultHelper::json(200, '领取成功', $model);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return ResultHelper::json(422, $e->getMessage());
        }
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