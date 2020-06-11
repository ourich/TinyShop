<?php

namespace addons\TinyShop\api\modules\v1\controllers\commission;

use common\helpers\ResultHelper;
use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use api\controllers\UserAuthController;
use addons\TinyShop\common\models\member\MemberTixian;
use common\enums\StatusEnum;
use common\models\forms\CreditsLogForm;

/**
 * 意见反馈
 *
 * Class MemberTixianController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class TixianController extends UserAuthController
{
    /**
     * @var MemberTixian
     */
    public $modelClass = MemberTixian::class;

    /**
     * 首页
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['member_id' => Yii::$app->user->identity->member_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc'),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
    }

    /**
     * @return array|mixed|\yii\db\ActiveRecord
     */
    public function actionCreate()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass();
        $model->attributes = Yii::$app->request->post();
        Yii::$app->services->memberCreditsLog->decrMoney(new CreditsLogForm([
            'member' => Yii::$app->services->member->get(Yii::$app->user->identity->member_id),
            'num' => $model->money,
            'credit_group' => 'tixian',
            'map_id' => $model->id,
            'remark' => '申请提现',
        ]));
        if (!$model->save()) {
            return ResultHelper::json(422, $this->getError($model));
        }

        return $model;
    }

    /**
     * 单个显示
     *
     * @param $id
     * @return \yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        !is_array($model->account_img) && $model->account_img = Json::decode($model->account_img);

        return $model;
    }

    /**
     * @return array
     */
    public function actionType()
    {
        return [
            1 => '支付宝',
            2 => '微信',
            3 => '银行卡',
        ];
    }
}