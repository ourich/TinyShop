<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use addons\TinyShop\merchant\forms\OilCardForm;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;

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

    /**
     * 卡片转出初始化
     * @return [type] [description]
     */
    public function actionBegin()
    {
        if (!($model = $this->modelClass::find()->where([
                'member_id' => Yii::$app->user->identity->member_id,
                'status' => StatusEnum::ENABLED,
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('您的卡库是空的哦');
        }

        return $model;

    }

    /**
     * 执行转出
     * @return [type] [description]
     */
    public function actionChange()
    {
        $data = Yii::$app->request->post();
        $member_id = Yii::$app->user->identity->member_id;
        //检查收款人是否存在
        $tomember = Yii::$app->services->member->findByMobile($data['mobile']);
        if (!$tomember) {
            throw new NotFoundHttpException('接收人不存在');
        }
        //获得转出的卡片列表
        $list = $this->modelClass::find()
            ->select('cardNo')
            ->Where(['between','cardNo', $data['cardNo'], $data['endNo']])
            ->andWhere(['or', ['<>', 'member_id', $member_id], ['status' => StatusEnum::DISABLED]])
            ->asArray()
            ->all();
        //检查这些卡片的归属和状态
        if ($list) {
            throw new NotFoundHttpException('包含不属于您或者已激活的卡');
        }

        //执行转出
        return Yii::$app->tinyShopService->card->give($tomember['id'], $data['cardNo'], $data['endNo']);
    }
}