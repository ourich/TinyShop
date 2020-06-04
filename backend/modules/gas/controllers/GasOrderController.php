<?php

namespace addons\TinyShop\backend\modules\gas\controllers;

use Yii;
use addons\TinyShop\common\models\gas\GasOrder;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* GasOrder
*
* Class GasOrderController
* @package addons\TinyShop\backend\modules\gas\controllers
*/
class GasOrderController extends BaseController
{
    use Curd;

    /**
    * @var GasOrder
    */
    public $modelClass = GasOrder::class;


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
