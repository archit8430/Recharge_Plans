<?php

namespace App\Listeners;

use App\Events\ApiHit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleApiHit
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
     * @param  \App\Events\ApiHit  $event
     * @return void
     */
    public function handle(ApiHit $event)
    {
        //
    }
}
