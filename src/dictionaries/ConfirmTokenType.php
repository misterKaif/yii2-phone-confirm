<?php

namespace Teimur\YiiPhoneConfirm\dictionaries;

use Yii;

abstract class ConfirmTokenType extends BaseDictionary
{
    const SMS = 1;
    const EMAIL = 2;
    
    public static function all(): array
    {
        return [
            self::SMS   => Yii::t('app', 'Sms'),
            self::EMAIL => Yii::t('app', 'Email'),
        ];
    }
}
