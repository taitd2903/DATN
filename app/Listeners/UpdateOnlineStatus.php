<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateOnlineStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        if ($event instanceof Login) {
            $event->user->is_online = true;
        } elseif ($event instanceof Logout) {
            $event->user->is_online = false;
        }
    
        $event->user->save();
    }
}
