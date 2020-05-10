<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use addons\TinyShop\common\models\common\OilOrder;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* OilOrder
*
* Class OilOrderController
* @package addons\TinyShop\backend\modules\common\controllers
*/
class OilOrderController extends BaseController
{
    use Curd;

    /**
    * @var OilOrder
    */
    public $modelClass = OilOrder::class;


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
