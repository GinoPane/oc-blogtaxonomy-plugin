<?php

namespace GinoPane\BlogTaxonomy\Console;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\MigrationFactory;

class MigrateFromPlugin extends Command
{
    const NAME = 'blogtaxonomy:migratefromplugin';

    /**
     * @var string The console command name.
     */
    protected $name = 'blogtaxonomy:migratefromplugin';

    /**
     * @var string The console command description.
     */
    protected $description = 'Migrate from another plugin to the Blog Taxonomy';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        try {
            MigrationFactory::resolve($this->argument('plugin'), $this)
                ->migrate();
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            [
                'plugin',
                InputArgument::REQUIRED,
                'A plugin to migrate from. Supported plugins are: PKleindienst.BlogSeries'
            ]
        ];
    }
}
