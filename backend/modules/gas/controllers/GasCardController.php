<?php

namespace addons\TinyShop\backend\modules\gas\controllers;

use Yii;
use addons\TinyShop\common\models\gas\GasCard;
use common\traits\Curd;
use common\models\base\SearchModel;
use backend\controllers\BaseController;

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
    public $modelClass = GasCard::class;


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
