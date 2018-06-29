<?php

namespace mnshankar\CSV;

use Illuminate\Support\Facades\Facade;

/**
 * Class CSVFacade
 *
 * @package mnshankar\CSV
 */
class CSVFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'csv';
    }
}
