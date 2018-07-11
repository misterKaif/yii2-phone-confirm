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
    public $type;
    private $_token;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'trim'],
            [['phone', 'token'], 'required'],
            [['phone'], PhoneInputValidator::class],
            [['phone'], 'exist', 'targetClass' => Config::getUserClass(), 'targetAttribute' => 'phone'],
        ];
    }
    
    public function afterValidate()
    {
        parent::afterValidate(); // TODO: Change the autogenerated stub
    
        /** @var ConfirmTokenService $service */
        $service = Yii::createObject(ConfirmTokenService::class);
    
        try {
            $this->_token = $service->validateSmsToken($this->phone, $this->token);
        } catch (\Exception $e) {
            $this->addError('token', $e->getMessage());
            return false;
        }

    }
    
    public function getToken()
    {
        return $this->_token;
    }
    
    public function tokenActionIs($action)
    {
        return $this->_token->action == $action;
    }
    
}
