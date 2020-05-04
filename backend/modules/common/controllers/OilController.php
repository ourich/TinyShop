<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use addons\TinyShop\common\models\common\OilStations;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\TinyShop\backend\controllers\BaseController;

/**
* OilStations
*
* Class OilController
* @package addons\TinyShop\backend\modules\common\controllers
*/
class OilController extends BaseController
{
    use Curd;

    /**
    * @var OilStations
    */
    public $modelClass = OilStations::class;


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
