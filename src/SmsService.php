<?php

namespace Teimur\YiiPhoneConfirm;

use Namshi\Notificator\Notification\Sms\SmsNotification;
use Teimur\YiiPhoneConfirm\entities\ConfirmToken;
use Yii;

class SmsService extends NotificationService
{
    public function sendSms($phone, ConfirmToken $token)
    {
        $this->send(new SmsNotification(
            $phone,
            Yii::t('app', "Code: {token}", ['token' => $token->token])
        ));
    }
    
    public function sendResetPasswordConfirm($phone, ConfirmToken $token)
    {
        $notificator = new SmsNotification(
            $phone,
            Yii::t('app', 'Password reset code: {token}', ['token' => $token->token])
        );
        
        $this->send($notificator);
    }
    
    public function sendMemberInvitePassword($phone, string $password)
    {
        $this->send(new SmsNotification(
            $phone,
            Yii::t('app', 'Your password for UnicastApp is: {password}', compact('password'))
        ));
    }
    
    public function sendChangePhoneConfirm($phone, ConfirmToken $token)
    {
        $this->send(new SmsNotification(
            $phone,
            Yii::t('app', "New phone number confirmation code: {token}", ['token' => $token->token])
        ));
    }
}
