<?php

namespace addons\TinyShop\merchant\forms;

use Yii;
use common\helpers\ArrayHelper;
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

    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['defaultCount', 'reissuenNum'], 'integer', 'min' => 0];

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'defaultCount' => '已发放数量',
            'reissuenNum' => '增发数量',
        ]);
    }
    
}