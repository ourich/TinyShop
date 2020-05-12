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
        $model = $this->modelClass::find()
            ->orderBy('id asc')
            ->andWhere(['member_id' => 0])
            ->one();
        //创建时候该字段有默认值
        $model->giveNum='5';
        if ($model->load($request->post())) {
            if (($model->cardNo) && ($model->giveNum) && ($model->endNo)) {

                // [名称, 字段名, 类型, 类型规则]
                $header = [
                    ['卡号', 'cardNo'], // 规则不填默认text
                    ['密码', 'code', 'function', function($model){
                        return 'http://rf.com/html5/tiny-shop/index?code=' . $model['code'];
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
            
            return $this->redirect(['index']);
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
        $model = $this->modelClass::find()
            ->orderBy('id asc')
            ->andWhere(['member_id' => 0])
            ->one();
        
        if ($model->load($request->post())) {
            if (($model->cardNo) && ($model->giveNum) && ($model->member_id) && ($model->member->mobile)) {
                // 如果会员存在
                // Yii::$app->debris->p($model); 
                Yii::$app->tinyShopService->card->give($model->member_id, $model->cardNo, $model->endNo);
            }
            return $this->redirect(['index']);
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
