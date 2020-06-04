<?php

namespace addons\TinyShop\services\gas;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\forms\GasCardForm;
use yii\web\NotFoundHttpException;
use common\helpers\StringHelper;

/**
 * Class TransmitService
 * @package addons\TinyShop\services\common
 * @author jianyan74 <751393839@qq.com>
 */
class CardService extends Service
{

    /**
     * @param $topic_id
     * @param $topic_type
     * @param $member_id
     * @return Transmit|array|\yii\db\ActiveRecord|null
     */
    public function give(GasCardForm $model)
    {
        $rows = [];
        $min = $model->give_begin;
        $max = $model->give_end;
        for ($i = $min; $i <= $max; $i++) {
            $rows[] = [
                'cardNo' => $i,
                'member_id' => $model->give_to,
                'code' => StringHelper::random(6),
                'created_at' => time(),
                'status' => 1,
            ];
        }

        $field = ['cardNo', 'member_id', 'code', 'created_at', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(GasCardForm::tableName(), $field, $rows)->execute();

        return $model;
    }

}