<?php

namespace addons\TinyShop\frontend\controllers;

use Yii;
use common\controllers\AddonsController;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package addons\TinyShop\frontend\controllers
 */
class DefaultController extends BaseController
{
    /**
    * 首页
    *
    * @return string
    */
    public function actionIndex()
    {
        //测试入口
        $mobile = '13098878085';
        $test = Yii::$app->tinyShopService->czb->login($mobile);
        p($test);
        die();




        return $this->render('index',[

        ]);
    }
}