<?php

namespace Teimur\YiiPhoneConfirm\dictionaries;

use Yii;

abstract class ConfirmTokenAction extends BaseDictionary
{
    const CONFIRM_REGISTRATION = 1;
    const RESET_PASSWORD = 2;

    public static function all(): array
    {
        return [
            self::CONFIRM_REGISTRATION => Yii::t('app', 'Confirm registration')
        ];
    }
}
