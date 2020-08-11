<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\IngresoNotification;

class IngresoListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        User::all()
        ->except($event->ingreso->iduser)
        ->each(function(User $user) use ($event){
              //  $user->notify(new IngresoNotification($ingreso));
              Notification::send($user, new IngresoNotification($event->ingreso));
        });

    }
}
