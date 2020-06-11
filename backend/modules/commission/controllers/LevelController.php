<?php

namespace addons\TinyShop\backend\modules\commission\controllers;

use Yii;
use addons\TinyShop\common\models\commission\CommissionLevel;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* CommissionLevel
*
* Class LevelController
* @package addons\TinyShop\backend\modules\commission\controllers
*/
class LevelController extends BaseController
{
    use Curd;

    /**
    * @var CommissionLevel
    */
    public $modelClass = CommissionLevel::class;


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
}
