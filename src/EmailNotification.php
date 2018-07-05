<?php
namespace Teimur\YiiPhoneConfirm;


use Namshi\Notificator\Notification;
use Namshi\Notificator\Notification\Email\SwiftMailer\SwiftMailerNotificationInterface;
use Yii;

class EmailNotification extends Notification implements SwiftMailerNotificationInterface
{
    /**
     * Constructor.
     * @param string $emailTemplate
     * @param mixed $recipientAddresses
     * @param string $subject
     * @param array $parameters
     * @param string|null $filename
     * @param array $fileOptions
     */
    public function __construct(string $emailTemplate, $recipientAddresses, string $subject, array $parameters = [],
                                string $filename = null, array $fileOptions = [])
    {
        $message = Yii::$app->mailer->compose($emailTemplate, $parameters)
            ->setSubject($subject)
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($recipientAddresses);

        if (!empty($filename)) {
            $message->attach($filename, $fileOptions);
        }

        parent::__construct($message, $parameters);
    }
}
