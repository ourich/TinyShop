<?php

namespace addons\TinyShop\api\modules\v1\controllers\member;

use Yii;
use yii\data\ActiveDataProvider;
use addons\TinyShop\merchant\forms\OilCardForm;
use api\controllers\UserAuthController;
use common\enums\StatusEnum;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use Da\QrCode\QrCode;
use yii\rest\Serializer;

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

    public function actionIndex()
    {
        $data = new ActiveDataProvider([
            'query' => $this->modelClass::find()
                ->where(['status' => StatusEnum::ENABLED, 'member_id' => Yii::$app->user->identity->member_id])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->orderBy('id desc')
                ->asArray(),
            'pagination' => [
                'pageSize' => $this->pageSize,
                'validatePage' => false,// 超出分页不返回data
            ],
        ]);
        // 主要生成header的page信息
        $models = (new Serializer())->serialize($data);
        foreach ($models as &$model) {
            if (empty($model['img'])) {
                $model['img'] = $this->getQr($model['cardNo']);
            }
        }

        return $models;
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
     * 生成二维码
     * @param  [type] $cardNo [description]
     * @return [type]         [description]
     */
    public function getQr($cardNo)
    {
        if (!($model = $this->modelClass::find()->where([
                'cardNo' => $cardNo
            ])->andFilterWhere(['merchant_id' => $this->getMerchantId()])->one())) {
            throw new NotFoundHttpException('卡片不存在');
        }
        if (!$model->img) {
            $qrCode = (new QrCode('This is my text'))
                ->setSize(250)
                ->setMargin(5)
                ->useForegroundColor(51, 153, 255);

            // 把图片保存到文件中:
            $qrCode->writeFile(Yii::getAlias('@attachment') . '/code/' . $cardNo . '.png'); // 没有指定的时候默认为png格式
            $model->img = Yii::$app->request->hostInfo . '/attachment/code/' . $cardNo . '.png';
            $model->save();
        }
        // Yii::warning($model->img);
        
        return $model->img;
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