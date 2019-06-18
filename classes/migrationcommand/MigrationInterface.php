<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand;

/**
 * Class MigrationInterface
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand
 */
interface MigrationInterface
{
    /**
     * @return void
     */
    public function migrate();
}