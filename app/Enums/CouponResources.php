<?php

namespace App\Enums;

class CouponResources
{
    const PURCHASED       = 1;
    const COMPENSATION    = 2;
    const PERIODIC        = 3;

    public static function getName($value)
    {
        $constants = array_flip((new \ReflectionClass(self::class))->getConstants());
        return $constants[$value] ?? null;
    }

    public static function getTranslatedName($value)
    {
        return __("messages.couponsResources.".self::getName($value));
    }

    public static function getValue($name)
    {
        return defined('self::' . $name) ? constant('self::' . $name) : null;
    }
    public static function getValues()
    {
        return [
            self::PURCHASED,
            self::COMPENSATION,
            self::PERIODIC,
        ];
    }
}
