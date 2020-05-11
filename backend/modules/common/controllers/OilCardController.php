<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;

use common\traits\Curd;
use common\models\base\SearchModel;
use addons\TinyShop\merchant\forms\OilCardForm;
use common\helpers\ResultHelper;
use backend\controllers\BaseController;

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
            'partialMatchAttributes' => [], // 模糊查询
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
    // public function actionEdit()
    // {
    //     $request = Yii::$app->request;
    //     $id = $request->get('id', null);
    //     $model = $this->findModel($id);
    //     if ($model->load($request->post()) && $model->save()) {
    //         return $this->redirect(['index']);
    //     }

    //     return $this->render($this->action->id, [
    //         'model' => $model,
    //     ]);
    // }

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
