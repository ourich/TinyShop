<?php

namespace addons\TinyShop\common\enums;

use common\enums\BaseEnum;

/**
 * 评价状态
 *
 * Class TixianStatusEnum
 * @package addons\TinyShop\common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TixianStatusEnum extends BaseEnum
{
    const DEAULT = 0;
    const PAYED = 1;
    const BOHUI = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DEAULT => '待处理',
            self::PAYED => '已打款',
            self::BOHUI => '已驳回',
        ];
    }
}