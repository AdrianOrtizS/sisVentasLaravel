<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\IngresoEvent;
use App\Listeners\IngresoListener;
use App\Events\VentaEvent;
use App\Listeners\VentaListener;
use App\Events\StockEvent;
use App\Listeners\StockListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        IngresoEvent::class => [
            IngresoListener::class,
        ],
        VentaEvent::class => [
           VentaListener::class,
        ],
        StockEvent::class => [
           StockListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}
