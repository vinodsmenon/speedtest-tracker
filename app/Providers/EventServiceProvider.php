<?php

namespace App\Providers;

use App\Events\SpeedtestCompleted;
use App\Events\SpeedtestFailed;
use App\Events\SpeedtestStarted;
use App\Listeners\ClearApplicationCache;
use App\Listeners\Data\InfluxDb2Listener;
use App\Listeners\SpeedtestCompletedListener;
use App\Listeners\Threshold\AbsoluteListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelSettings\Events\SettingsSaved;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        SettingsSaved::class => [
            ClearApplicationCache::class,
        ],

        SpeedtestCompleted::class => [
            SpeedtestCompletedListener::class,

            // Data listeners
            InfluxDb2Listener::class,

            // Notification listeners
            AbsoluteListener::class,
        ],

        SpeedtestFailed::class => [
            // nothing yet...
        ],

        SpeedtestStarted::class => [
            // nothing yet...
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
