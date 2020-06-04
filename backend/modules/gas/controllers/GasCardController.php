<?php

namespace addons\TinyShop\backend\modules\gas\controllers;

use Yii;
use addons\TinyShop\common\models\forms\GasCardForm;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;
use common\helpers\ResultHelper;

/**
* GasCard
*
* Class GasCardController
* @package addons\TinyShop\backend\modules\gas\controllers
*/
class GasCardController extends BaseController
{
    use Curd;

    /**
    * @var GasCard
    */
    public $modelClass = GasCardForm::class;


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
            'relations' => ['member' => ['mobile'], 'follower' => ['mobile']],
            'partialMatchAttributes' => ['member.mobile'], // 模糊查询
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
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionGive()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = $this->findModel($id);
        $min = $this->modelClass::find()->max('cardNo');   //目前卡号最大值
        $model->give_begin = $min ? $min+1 : '10000001';   //+1作为本次起始卡号
        $model->give_end = $model->give_begin + $model->give_num -1;   //+1作为本次起始卡号
        //获取的数据，传送给sever处理
        
        if ($model->load($request->post())) {
            $give_to = Yii::$app->services->member->findByMobile($model->give_to);
            if (!$give_to) {
                return $this->message('接收方不存在', $this->redirect(['give']), 'error');
            }
            $model->give_to = $give_to['id'];
            Yii::$app->tinyShopService->card->give($model);
            return $this->message('分配成功', $this->redirect(['index']));
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
