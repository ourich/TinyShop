<?php

namespace addons\TinyShop\services\gas;

use Yii;
use common\components\Service;
use addons\TinyShop\common\models\forms\GasCardForm;
use yii\web\NotFoundHttpException;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
use common\enums\StatusEnum;

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
        //已存在的卡片(包含边界)
        $cards =  GasCardForm::find()
            ->where(['status' => StatusEnum::ENABLED])
            ->andWhere(['between', 'cardNo', $min, $max])
            ->orderBy('id desc')
            ->asArray()
            ->all();
        $cardids = ArrayHelper::getColumn($cards, 'id');
        $ids=implode(',',$cardids);
        Yii::$app->db->createCommand()->update(GasCardForm::tableName(), ['member_id' => $model->give_to], "id in ($ids)")->execute();
        //更新存在的卡片，创建不存在的
        $cardNos = ArrayHelper::index($cards, 'cardNo');
        for ($i = $min; $i <= $max; $i++) {
            if (ArrayHelper::keyExists($i, $cardNos, false)) {
                continue;
            }
            $rows[] = [
                'cardNo' => $i,
                'member_id' => $model->give_to,
                'code' => StringHelper::random(6),
                'created_at' => time(),
                'status' => 1,
            ];
        }
        // p($rows);
        // die();

        $field = ['cardNo', 'member_id', 'code', 'created_at', 'status'];
        !empty($rows) && Yii::$app->db->createCommand()->batchInsert(GasCardForm::tableName(), $field, $rows)->execute();

        return $model;
    }

}