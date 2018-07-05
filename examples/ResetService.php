<?php

//namespace common\services\user;

//use common\base\Assert;
//use common\entities\user\User;
use Teimur\YiiPhoneConfirm\ConfirmTokenService;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenAction;
use Teimur\YiiPhoneConfirm\dictionaries\ConfirmTokenType;
use Teimur\YiiPhoneConfirm\forms\ResetByPhoneForm;
use Teimur\YiiPhoneConfirm\SmsService;
use yii\base\Component;

class ResetService extends Component
{
    private $user;
    private $tokenService;
    private $smsService;
    
    public function __construct(
        ConfirmTokenService $tokenService,
        SmsService $smsService,
        array $config = []
    )
    {
        parent::__construct($config);
        $this->user = \Yii::$app->user;
        $this->tokenService = $tokenService;
        $this->smsService = $smsService;
    }
    
    public function resetByPhone(ResetByPhoneForm $form): User
    {
        Assert::false($form->hasErrors());
        
        $item = User::byPhone($form->phone);
        
        $token = $this->tokenService->generate($item->id, ConfirmTokenType::SMS, ConfirmTokenAction::RESET_PASSWORD);
        $this->smsService->sendResetPasswordConfirm($item, $token);
        
        return $item;
    }
    
    public function checkToken($phone, $token)
    {
        $user = User::byPhone($phone);
        
        Assert::notExist($user);
        
        $result = $this->tokenService->checkToken(
            ConfirmTokenAction::RESET_PASSWORD,
            ConfirmTokenType::SMS,
            $user->id,
            $token);
        
        return $result;
    }
    
    public function deleteToken($phone, $token)
    {
        $this->tokenService->deleteToken($phone, $token);
    }
    
}
