<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand\Exceptions;

use Exception;

/**
 * Class NoPluginException
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand\Exceptions
 */
class NoPluginException extends Exception
{
    public function __construct(string $pluginName)
    {
        parent::__construct("$pluginName plugin does not exist in the system. Please check if it is installed and try again.");
    }
}