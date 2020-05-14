<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;

use common\traits\Curd;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\forms\OilCardForm;
use common\helpers\ResultHelper;
use backend\controllers\BaseController;
use common\helpers\ExcelHelper;
use common\helpers\Url;
use common\enums\StatusEnum;

/**
* OilCard
*
* Class OilCardController
* @package addons\TinyShop\backend\modules\common\controllers
*/
class OilCardController extends BaseController
{
    use Curd;

    /**
    * @var OilCard
    */
    public $modelClass = OilCardForm::class;


    /**
    * 首页
    *
    * @return string
    * @throws \yii\web\NotFoundHttpException
    */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'relations' => ['member' => ['mobile']],
            'partialMatchAttributes' => ['code', 'member.mobile'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 导出
     *
     * @return mixed
     */
    public function actionPrint()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        $min = $this->modelClass::find()->max('cardNo');   //目前卡号最大值
        $model->cardNo = $min ?? '100000001';
        //创建时候该字段有默认值
        $model->giveNum='10000';
        if ($model->load($request->post())) {
            if (!$model->cardNo || !$model->endNo || !$model->giveNum) {
                return $this->message('请填写完整', $this->redirect(['send']), 'error');
            }
            // [名称, 字段名, 类型, 类型规则]
            $header = [
                ['卡号', 'cardNo'], // 规则不填默认text
                ['密码', 'code', 'function', function($model){
                    return 'http://rf.com/' . 'pages/public/register?promo_code=' . $model['code'];
                }],
            ];

            $list = $this->modelClass::find()
                ->select('cardNo,code')
                ->andFilterWhere(['between','cardNo', $model->cardNo, $model->endNo])
                ->orderBy('id asc')
                ->asArray()
                ->all();
            // 简单使用
            return ExcelHelper::exportData($list, $header);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionSend()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        $min = $this->modelClass::find()->max('cardNo');   //目前卡号最大值
        $model->cardNo = $min ?? '100000000';
        $model->cardNo += 1;
        
        if ($model->load($request->post())) {
            //检查收款人是否存在
            $tomember = Yii::$app->services->member->findByMobile($model->mobile);
            if (!$tomember) {
                return $this->message('接收人不存在', $this->redirect(['send']), 'error');
            }
            $model->member_id = $tomember['id'];
            if (!$model->cardNo || !$model->giveNum) {
                return $this->message('请填写起始卡号和数量', $this->redirect(['send']), 'error');
            }
            // Yii::$app->debris->p($model); 
            Yii::$app->tinyShopService->card->create($model);
            return $this->message('卡片分配成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 卡片交换
     *
     * @return mixed
     */
    public function actionChange()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        $min = $this->modelClass::find()->max('cardNo');   //目前卡号最大值
        $model->cardNo = '100000000';
        $model->mobiler_begin = '100000000';
        
        if ($model->load($request->post())) {
            //检查收款人是否存在
            $mobile = Yii::$app->services->member->findByMobile($model->mobile);
            $mobiler = Yii::$app->services->member->findByMobile($model->mobiler);
            if (!$mobile || !$mobiler) {
                return $this->message('手机号错误', $this->redirect(['send']), 'error');
            }
            $model->member_id = $mobile['id'];
            if (!$model->cardNo || !$model->endNo || !$model->mobiler_begin || !$model->mobiler_end) {
                return $this->message('请填写起始卡号和数量', $this->redirect(['send']), 'error');
            }
            // Yii::$app->debris->p($model); 
            //检查归属权和使用状态
            $list = $this->modelClass::find()
                ->select('cardNo')
                ->Where(['between','cardNo', $model->cardNo, $model->endNo])
                ->andWhere(['or', ['<>', 'member_id', $model->member_id], ['status' => StatusEnum::DISABLED]])
                ->asArray()
                ->all();
            if ($list) {
                throw new NotFoundHttpException('包含不属于他或者已激活的卡');
            }
            Yii::$app->tinyShopService->card->give($mobiler['id'], $model->cardNo, $model->endNo);   //扣减库存
            Yii::$app->tinyShopService->card->give($model->member_id, $model->mobiler_begin, $model->mobiler_end);   //重新分配给他
            return $this->message('卡片交换成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax批量增发
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdd()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $num = $request->post('num');
            Yii::$app->tinyShopService->card->create($num);
            return ResultHelper::json(200, "增发".$num . '张卡片成功,请刷新查看！');
        }

        throw new NotFoundHttpException('请求出错!');
    }

}
