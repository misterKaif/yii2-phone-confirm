<?php

namespace Teimur\YiiPhoneConfirm;

use Teimur\YiiPhoneConfirm\entities\ConfirmToken;

use Yii;

class EmailService extends NotificationService
{
    public function sendTokenConfirm($email, ConfirmToken $token)
    {
        $this->send(new EmailNotification(
            'emailConfirm',
            $email,
            Yii::t('app', 'Email confirmation'),
            compact('token')
        ));
    }
}
