<?php
namespace Teimur\YiiPhoneConfirm;

use Namshi\Notificator\NotificationInterface;
use Teimur\YiiPhoneConfirm\jobs\SendNotificationJob;
use Yii;

abstract class NotificationService
{
    protected final function send(NotificationInterface $notification)
    {
        $job = new SendNotificationJob($notification);
        if (!YII_ENV_TEST) {
            Yii::$app->queue->push($job);
        }
    }
}
