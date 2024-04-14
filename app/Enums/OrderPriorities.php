<?php

namespace App\Enums;

class OrderPriorities
{
    const ULTIMATE = 1;
    const HIGH     = 2;
    const priority = 3;

    public static function getNameArabic($priority)
    {
        switch ($priority) {
            case 1:
                return "أولوية قصوى";
            case 2:
                return "أولوية عالية";
            case 3:
                return "أولوية";
        }
        return "بدون أولوية";
    }

    public static function getName($value)
    {
        $constants = array_flip((new \ReflectionClass(self::class))->getConstants());

        return $constants[$value] ?? null;
    }

    public static function getValue($name)
    {
        return defined('self::' . $name) ? constant('self::' . $name) : null;
    }
    public static function getValues()
    {
        return [
            self::ULTIMATE,
            self::HIGH,
            self::priority,
        ];
    }
}
