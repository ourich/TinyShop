<?php

namespace addons\TinyShop\backend\modules\common\controllers;

use Yii;
use addons\TinyShop\common\models\common\OilDelivery;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

/**
* OilDelivery
*
* Class OilDeliveryController
* @package addons\TinyShop\backend\modules\common\controllers
*/
class OilDeliveryController extends BaseController
{
    use Curd;

    /**
    * @var OilDelivery
    */
    public $modelClass = OilDelivery::class;


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
