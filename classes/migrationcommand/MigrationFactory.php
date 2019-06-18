<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand;

use GinoPane\BlogTaxonomy\Classes\MigrationCommand\Exceptions\NoPluginException;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations\NullMigration;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations\PkleindienstBlogSeriesMigration;
use Illuminate\Console\Command;

/**
 * Class MigrationFactory
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand
 */
class MigrationFactory
{
    /**
     * @param string $plugin
     *
     * @throws NoPluginException
     *
     * @return MigrationInterface
     */
    public static function resolve(string $plugin, Command $command): MigrationInterface
    {
        $plugin = strtolower($plugin);
        $migration = new NullMigration($command);

        switch ($plugin) {
            case strtolower(PkleindienstBlogSeriesMigration::PLUGIN_NAME):
                $migration = new PkleindienstBlogSeriesMigration($command);
        }

        return $migration;
    }
}