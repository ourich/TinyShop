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
        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
