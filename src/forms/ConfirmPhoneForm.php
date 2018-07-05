<?php

namespace Teimur\YiiPhoneConfirm\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
use Teimur\YiiPhoneConfirm\Config;
use Teimur\YiiPhoneConfirm\ConfirmTokenService;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ConfirmPhoneForm extends Model
{
    public $phone;
    public $token;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            [['phone', 'token'], 'required'],
            [['phone'], PhoneInputValidator::class],
            [['phone'], 'exist', 'targetClass' => Config::getUserClass(), 'targetAttribute' => ['phone' => 'phone']],
        ];
    }
    
    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub
    
        /** @var ConfirmTokenService $service */
        $service = Yii::createObject(ConfirmTokenService::class);
    
        try {
            $service->validateSmsToken($this->phone, $this->token);
        } catch (\Exception $e) {
            $this->addError('token', $e->getMessage());
            return false;
        }
    
    }
    
}
