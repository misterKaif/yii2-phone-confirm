<?php

namespace Teimur\YiiPhoneConfirm\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
use Teimur\YiiPhoneConfirm\Config;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ResetByPhoneForm extends Model
{
    public $phone;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            ['phone', 'required'],
            [['phone'], PhoneInputValidator::class],
            [['phone'], 'exist', 'targetClass' => Config::getUserClass(), 'targetAttribute' => 'phone'],
        ];
    }
}
