<?php

namespace App\Enums;

class RanksFeatures
{
    const COUPON_PER_MONTH   = "coupon_per_month";
    const DISCOUNT_ON_DELIVER   = "discount_on_deliver";
    const DELIVER_PRIORITY   = "priority";

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
            self::COUPON_PER_MONTH,
            self::DISCOUNT_ON_DELIVER,
            self::DELIVER_PRIORITY,
        ];
    }
}
