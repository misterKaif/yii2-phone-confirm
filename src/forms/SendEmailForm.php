<?php

namespace Teimur\YiiPhoneConfirm\forms;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\entities\user\User;
use Teimur\YiiPhoneConfirm\Config;
use Teimur\YiiPhoneConfirm\ConfirmTokenService;
use Yii;
use yii\base\Model;
use yii\web\HttpException;

/**
 * Login form
 */
class SendEmailForm extends Model
{
    public $email;
    
    /** @var ConfirmTokenService object */
    private $service;
    
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->service = Yii::createObject(ConfirmTokenService::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            [['email'], 'exist', 'targetClass' => Config::getUserClass(), 'targetAttribute' => 'email'],
            ['email', 'validateEmail']
        ];
    }
    
    public function validateEmail()
    {
        try {
            $this->service->validateEmail($this->email);
        } catch (HttpException $e) {
            $this->addError('phone', $e->getMessage());
            return false;
        }
    }
}
