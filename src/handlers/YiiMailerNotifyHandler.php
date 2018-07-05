<?php

namespace Teimur\YiiPhoneConfirm\handlers;

use Namshi\Notificator\Notification\Handler\HandlerInterface;
use Namshi\Notificator\NotificationInterface;
use Teimur\YiiPhoneConfirm\EmailNotification;
use Yii;

class YiiMailerNotifyHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     */
    function shouldHandle(NotificationInterface $notification)
    {
        return $notification instanceof EmailNotification;
    }

    /**
     * @inheritDoc
     */
    function handle(NotificationInterface $notification)
    {
        Yii::$app->mailer->send($notification->getMessage());
    }
}
