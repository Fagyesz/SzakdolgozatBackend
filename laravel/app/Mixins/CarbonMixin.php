<?php

namespace App\Mixins;
use Closure;
use Illuminate\Support\Carbon;

/** @mixin Carbon */
class CarbonMixin
{
    public function nextMinuteMultipleOf(): Closure
    {
        return function (int $value){
            $remainder = $this->minute % $value;
            return $this->addMinutes(($remainder) ? $value - $remainder : 0);
        };
    }

    public function isAm(): Closure
    {
        return function () {
          return $this->hour < 12;
        };
    }

    public function isPm(): Closure
    {
        return function () {
            return $this->hour >= 12;
        };
    }
}
