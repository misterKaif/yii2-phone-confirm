<?php
namespace Teimur\YiiPhoneConfirm\dictionaries;

use Webmozart\Assert\Assert;
use Yii;

abstract class BaseDictionary
{
    abstract public static function all(): array;

    public static function get($key): string
    {
        $all = static::all();

        Assert::keyExists($all, $key);

        return Yii::$app->formatter->asRaw($all[$key]);
    }

    public static function keys(): array
    {
        return array_keys(static::all());
    }
}
