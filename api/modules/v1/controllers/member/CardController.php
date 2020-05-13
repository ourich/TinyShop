<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use addons\TinyShop\merchant\forms\OilCardForm;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;

/**
 * 发票
 *
 * Class InvoiceController
 * @package addons\TinyShop\api\modules\v1\controllers\member
 * @author jianyan74 <751393839@qq.com>
 */
class CardController extends UserAuthController
{
    /**
     * @var Invoice
     */
    public $modelClass = OilCardForm::class;

    /**
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionDefault()
    {
        return Yii::$app->tinyShopService->memberInvoice->findDefaultByMemberId(Yii::$app->user->identity->member_id);
    }
    public function actionBegin()
    {
        if (!($model = $this->modelClass::find()->where([
                'member_id' => Yii::$app->user->identity->member_id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('请求的数据不存在');
        }

        return $model;

    }
}