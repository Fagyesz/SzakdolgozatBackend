<?php

namespace App\Providers;

use App\Mixins\CarbonImmutableMixin;
use App\Mixins\CarbonMixin;
use App\Mixins\StrMixin;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;

/**
 * Class MixinServiceProvider
 * @package App\Providers
 *
 * Mixins are like Extension methods in C#, however there is no auto discover. You need to register each mixin here
 */
class MixinServiceProvider extends ServiceProvider
{
    protected array $mixins = [
        StrMixin::class => Stringable::class,
        CarbonMixin::class => Carbon::class,
        CarbonImmutableMixin::class => CarbonImmutable::class
    ];

    protected array $testingMixins = [];

    public function register()
    {
        foreach ($this->mixins as $mixin => $class) {
            $class::mixin(new $mixin);
        }

        if ($this->app->environment('testing')) {
            foreach ($this->testingMixins as $mixin => $class) {
                $class::mixin(new $mixin);
            }
        }
    }
}
