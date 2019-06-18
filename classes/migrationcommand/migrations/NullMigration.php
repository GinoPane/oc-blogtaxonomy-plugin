<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations;

/**
 * Class NullMigration
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations
 */
class NullMigration extends AbstractMigration
{
    /**
     * @return void
     */
    public function migrate()
    {
        $this->command->error('Found no suitable plugins for migration. Use help (-h, --help) to see available options.');
    }

    /**
     * No validation required for this null implementation
     */
    protected function validate()
    {
        return null;
    }

    /**
     * @return null
     */
    protected function migratePlugin()
    {
        return null;
    }
}