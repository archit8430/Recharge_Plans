<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Logging extends Facade{
    
    protected static function getFacadeAccessor()
    {
        return 'LoggingHelper';
    }

}

?>