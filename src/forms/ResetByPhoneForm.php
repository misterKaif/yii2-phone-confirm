<?php

namespace teimur8\yiiPhoneConfirm\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
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
            [['phone'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['phone' => 'phone']],
        ];
    }
}
