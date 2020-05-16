<?php

namespace addons\TinyShop\api\modules\v1\forms;

use Yii;
use yii\base\Model;
use common\helpers\RegularHelper;
use common\models\common\SmsLog;

/**
 * Class SmsCodeForm
 * @package api\modules\v1\forms
 * @author jianyan74 <751393839@qq.com>
 */
class SmsCodeForm extends Model
{
    /**
     * @var
     */
    public $mobile;

    /**
     * @var
     */
    public $usage;
    public $promo_code;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['mobile', 'usage'], 'required'],
            [['promo_code'], 'string'],
            [['mobile'], 'isRegister'],
            [['usage'], 'in', 'range' => array_keys(SmsLog::$usageExplain)],
            ['mobile', 'match', 'pattern' => RegularHelper::mobile(), 'message' => '请输入正确的手机号'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'usage' => '用途',
            'promo_code' => '邀请码',
        ];
    }

    /**
     * @param $attribute
     */
    public function isRegister($attribute)
    {
        if ($this->usage == SmsLog::USAGE_REGISTER && Yii::$app->services->member->findByMobile($this->mobile)) {
            //手机号已注册，且邀请码有效，则执行充值
            if (Yii::$app->tinyShopService->card->findByPromoCode($this->promo_code)) {
                Yii::$app->tinyShopService->card->useCard($this->mobile, $this->promo_code);
                $this->addError($attribute, '充值成功');
            } else {
                $this->addError($attribute, '该手机号码已注册');
            }
            
        }
    }

    /**
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function send()
    {
        $code = rand(1000, 9999);
        return Yii::$app->services->sms->send($this->mobile, $code, $this->usage);
    }
}