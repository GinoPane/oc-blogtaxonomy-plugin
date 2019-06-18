<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations;

use Illuminate\Console\Command;
use System\Classes\PluginManager;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\MigrationInterface;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\Exceptions\NoPluginException;

/**
 * Class PkleindienstBlogSeriesMigration
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations
 */
abstract class AbstractMigration implements MigrationInterface
{
    const PLUGIN_NAME = '';

    /**
     * @var Command
     */
    protected $command;

    /**
     * AbstractMigration constructor.
     *
     * @param Command $command
     *
     * @throws NoPluginException
     */
    public function __construct(Command $command)
    {
        $this->validate();

        $this->command = $command;
    }

    /**
     * @return void
     */
    public function migrate()
    {
        $this->command->alert('Migration from ' . static::PLUGIN_NAME);

        $this->migratePlugin();

        $this->command->info('Migration from ' . static::PLUGIN_NAME . ' finished');
    }

    abstract protected function migratePlugin();

    /**
     * @throws NoPluginException
     */
    protected function validate()
    {
        $pluginManager = PluginManager::instance();

        if (!$pluginManager->hasPlugin(static::PLUGIN_NAME)) {
            throw new NoPluginException(static::PLUGIN_NAME);
        }
    }
}