<?php

namespace App\Listeners;

use DateTime;
use App\AccessLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $access = new AccessLog;
        $access->user_id = $event->user->id;
        $access->ip_address = request()->ip();
        $access->login = new DateTime;
        $access->save();
    }
}
