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
     * 重组显示
     *
     * @param $model
     * @return mixed
     */
    public function regroupShow($model, $stations, $mobile)
    {
        // 是否可领取 
        // $other = OilStations::find()->select('gasAddress,gasAddressLatitude,gasAddressLongitude,gasLogoSmall')->where(['gasId'=>$model['gasId']])->one();
        $model['gasName'] = mb_substr($model['gasName'], 0, 15, 'utf-8');
        $model['gasAddress'] = $stations['gasAddress'];
        $model['gasAddressLongitude'] = $stations['gasAddressLongitude'];
        $model['gasAddressLatitude'] = $stations['gasAddressLatitude'];
        $model['gasLogoSmall'] = $stations['gasLogoSmall'];
        $model['juli'] = $stations['juli'];
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
     * @param $id
     * @return mixed|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        // 关联我已领取的优惠券
        $with = [];
        if (!Yii::$app->user->isGuest) {
            $with = ['myGet' => function(ActiveQuery $query) {
                return $query->andWhere(['member_id' => Yii::$app->user->identity->member_id]);
            }];
        }

        $model = $this->modelClass::find()
            ->where([
                'id' => $id,
                'merchant_id' => $this->getMerchantId(),
                'status' => StatusEnum::ENABLED,
            ])
            ->with(ArrayHelper::merge($with, ['usableProduct']))
            ->asArray()
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return Yii::$app->tinyShopService->marketingCouponType->regroupShow($model);
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