<?php

namespace Teimur\YiiPhoneConfirm\forms;

class PhoneSetPasswordForm extends ConfirmPhoneForm
{
    public $password;
    public $password_repeat;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        
        
        $rules = array_merge($rules, [
            ['password', 'trim'],
            [['password', 'password_repeat'], 'required'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat'],
            ['password', 'string', 'min' => 6]
        ]);
        
        return $rules;
        
    }
}
