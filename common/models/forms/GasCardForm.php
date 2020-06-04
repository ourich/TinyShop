<?php

namespace addons\TinyShop\common\models\forms;

use addons\TinyShop\common\models\gas\GasCard;
use common\helpers\ArrayHelper;

/**
 * Class OrderQueryForm
 * @package addons\TinyShop\common\models\forms
 * @author jianyan74 <751393839@qq.com>
 */
class GasCardForm extends GasCard
{
    public $give_to = '';
    public $give_num = '10';
    public $give_begin = '';
    public $give_end = '';

    /**
     * @return array
     */
    public function rules()
    {
        $rule = parent::rules();
        $rule[] = [['give_to', 'give_num', 'give_begin', 'give_end'], 'integer', 'min' => 0];

        return $rule;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'give_to' => '分配对象',
            'give_num' => '分配数量',
            'give_begin' => '起始卡号',
            'give_end' => '截止卡号',
        ]);
    }
}