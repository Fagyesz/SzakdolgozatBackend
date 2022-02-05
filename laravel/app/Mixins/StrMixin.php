<?php

namespace App\Mixins;

use Closure;
use Illuminate\Support\Stringable;

/**
 * @method Stringable endsWith($value)
 * @method Stringable append($value)
 */
class StrMixin
{
    public function ensureEnd(): Closure {
        return fn(string $value) => $this->endsWith($value) ? $this : $this->append($value);
    }
}
