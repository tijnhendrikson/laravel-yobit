<?php
namespace Pepijnolivier\Yobit;

use Illuminate\Support\Facades\Facade;

class Yobit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'yobit';
    }
}
