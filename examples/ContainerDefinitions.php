<?php

use Namshi\Notificator\Manager;
use Teimur\YiiPhoneConfirm\handlers\SmscNotifyHandler;
use Teimur\YiiPhoneConfirm\handlers\YiiMailerNotifyHandler;
use Yii;
use yii\base\BootstrapInterface;

class ContainerDefinitions implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $container->setSingleton(Manager::class, function(){
            $handlers[] = new SmscNotifyHandler(Yii::$app->params['smsc']['login'], Yii::$app->params['smsc']['password']);
            $handlers[] = new YiiMailerNotifyHandler();
            
            $manager = new Manager($handlers, Yii::$app->monolog->logger);
            
            return $manager;
        }
        );
        
    }
}
