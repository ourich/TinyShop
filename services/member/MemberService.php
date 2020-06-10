<?php

namespace addons\TinyShop\services\member;

use common\enums\StatusEnum;
use common\helpers\EchantsHelper;
use common\models\member\Member;
use common\components\Service;
use common\helpers\ArrayHelper;

/**
 * Class MemberService
 * @package addons\TinyShop\services\member
 * @author jianyan74 <751393839@qq.com>
 */
class MemberService extends Service
{
    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findById($id, $select = ['*'])
    {
        return Member::find()
            ->select($select)
            ->where(['id' => $id, 'status' => StatusEnum::ENABLED])
            ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
            ->one();
    }

    /**
     * 团队人数
     * @param  [type] $id     [description]
     * @param  array  $select [description]
     * @return [type]         [description]
     */
    public function countChilds($id)
    {
        $count = 0;
        $pid = $id;
        do {
            $childs = Member::find()
                ->where(['pid' => $pid, 'status' => StatusEnum::ENABLED])
                ->andFilterWhere(['merchant_id' => $this->getMerchantId()])
                ->asArray()
                ->all();
            //追加
            $count += count($childs);
            $pid = ArrayHelper::getColumn($childs, 'id');   //重置PID
        } while ( !empty($pid));

        return $count;
        
    }
}