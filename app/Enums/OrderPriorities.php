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
                return __("messages.Ranks.UltimatePriority");
            case 2:
                return __("messages.Ranks.HighPriority");
            case 3:
                return __("messages.Ranks.priority");
        }
        return __("messages.Ranks.noPriority");
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
