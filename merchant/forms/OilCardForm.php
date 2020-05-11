<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\models\member\Member;
use addons\TinyShop\common\models\common\OilCard;
use addons\TinyShop\common\enums\RangeTypeEnum;

/**
 * Class OilCardForm
 * @package addons\TinyShop\merchant\forms
 * @author jianyan74 <751393839@qq.com>
 */
class OilCardForm extends OilCard
{
    public $defaultCount = 0;
    public $reissuenNum = 0;
    public $giveNum = 0;
    public $endNo = 0;

    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['defaultCount', 'reissuenNum', 'giveNum', 'endNo'], 'integer', 'min' => 0];

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'defaultCount' => '起始卡号',
            'reissuenNum' => '增发数量',
            'giveNum' => '分配数量',
            'endNo' => '终点卡号',
        ]);
    }

    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }
    public function getUser()
    {
        return $this->hasOne(Member::class, ['id' => 'user']);
    }
    
}