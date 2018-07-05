<?php

namespace Teimur\YiiPhoneConfirm\jobs;


use Yii;
use yii\base\InvalidCallException;
use yii\base\Object;
use yii\queue\Job;

abstract class BaseJob extends Object implements Job
{
    public function execute($queue)
    {
        if (method_exists($this, 'executeByDI')) {
            Yii::$container->invoke([$this, 'executeByDI'], compact('queue'));
        } else {
            throw new InvalidCallException('Method executeByDI not exists.');
        }
    }
}
