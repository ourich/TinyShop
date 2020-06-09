<?php

namespace addons\TinyShop\api\modules\v1\controllers\gas;

use Yii;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\gas\GasCard;
use common\enums\StatusEnum;

/**
 * 我的优惠券
 *
 * Class CardController
 * @package addons\TinyShop\api\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CardController extends UserAuthController
{
    /**
     * @var Coupon
     */
    public $modelClass = GasCard::class;

    /**
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $state = Yii::$app->request->get('state', 1);

        $where = [
            'and',
            ['member_id' => Yii::$app->user->identity->member_id],
            ['status' => $state],
        ];

        $orderBy = 'id desc';

        return new ActiveDataProvider([
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