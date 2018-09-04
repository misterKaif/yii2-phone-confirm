<?php

namespace Teimur\YiiPhoneConfirm\entities;

use Teimur\YiiPhoneConfirm\Config;
use yii\db\ActiveQuery;

class User
{
    public static function find(): ActiveQuery
    {
        $userClass = Config::getUserClass();
        $queiry = $userClass::find();
        
        /** @var ActiveQuery */
        return $queiry;
    }
    
    public static function byPhone($phone)
    {
        return self::find()->where([
            'phone' => $phone
        ])->limit(1)->one();
    }
    
    public static function byEmail($email)
    {
        return self::find()->where([
            'email' => $email
        ])->limit(1)->one();
    }
}
