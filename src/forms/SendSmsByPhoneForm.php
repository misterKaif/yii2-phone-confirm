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
class SendSmsByPhoneForm extends Model
{
    public $phone;
    
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
            ['phone', 'trim'],
            ['phone', 'required'],
            [['phone'], PhoneInputValidator::class],
            [['phone'], 'exist', 'targetClass' => Config::getUserClass(), 'targetAttribute' => 'phone'],
            ['phone', 'validatePhone']
        ];
    }
    
    public function validatePhone()
    {
        try {
            $this->service->validatePhone($this->phone);
        } catch (HttpException $e) {
            $this->addError('phone', $e->getMessage());
            return false;
        }
    }
}
