<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineCustomIfStatements();
        $this->setApiRateLimit();

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        AboutCommand::add('Speedtest Tracker', fn () => [
            'Version' => config('speedtest.build_version'),
        ]);
    }

    /**
     * Define custom if statements, these were added to make the blade templates more readable.
     *
     * Ref: https://github.com/laravel/framework/pull/51561
     */
    protected function defineCustomIfStatements(): void
    {
        /**
         * Adds blank() custom if statement.
         */
        Blade::if('blank', function (mixed $value) {
            return blank($value);
        });

        /**
         * Adds filled() custom if statement.
         */
        Blade::if('filled', function (mixed $value) {
            return filled($value);
        });
    }

    protected function setApiRateLimit(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(config('api.rate_limit'));
        });
    }
}
