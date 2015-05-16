<?php

namespace Thujohn\Analytics;

use Illuminate\Support\Facades\Facade;

class AnalyticsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'analytics';
    }
}
