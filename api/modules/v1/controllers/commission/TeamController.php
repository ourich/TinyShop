<?php

namespace addons\TinyShop\api\modules\v1\controllers\commission;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use common\models\member\Member;
use common\enums\StatusEnum;
use yii\rest\Serializer;

/**
 * 我的优惠券
 *
 * Class TeamController
 * @package addons\TinyShop\api\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class TeamController extends UserAuthController
{
    /**
     * @var Coupon
     */
    public $modelClass = Member::class;

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        // $state = Yii::$app->request->get('state', 1);

        $where = [
            'and',
            ['pid' => Yii::$app->user->identity->member_id],
            ['status' => StatusEnum::ENABLED],
        ];

        $orderBy = 'id desc';

        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where($where)
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy($orderBy)
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
        // 主要生成header的page信息
        $models = (new Serializer())->serialize($data);
        foreach ($models as &$model) {
            $model['childs'] = Yii::$app->tinyShopService->member->countChilds($model['id']);
        }

        return $models;
    }

    /**
     * 清空已过期的优惠券
     *
     * @param $member_id
     */
    public function actionClear()
    {
        return Coupon::updateAll(['status' => StatusEnum::DELETE], [
            'member_id' => Yii::$app->user->identity->member_id,
            'status' => StatusEnum::ENABLED,
            'state' => Coupon::STATE_PAST_DUE
        ]);
    }
}