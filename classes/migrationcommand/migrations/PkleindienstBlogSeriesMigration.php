<?php

namespace GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations;

use Exception;
use Illuminate\Support\Facades\DB;
use October\Rain\Database\Collection;
use October\Rain\Support\Facades\Schema;
use October\Rain\Database\ModelException;
use PKleindienst\BlogSeries\Models\Series;
use GinoPane\BlogTaxonomy\Models\Series as BlogTaxonomySeries;
use GinoPane\BlogTaxonomy\Classes\MigrationCommand\Exceptions\NoDataException;

/**
 * Class PkleindienstBlogSeriesMigration
 *
 * @package GinoPane\BlogTaxonomy\Classes\MigrationCommand\Migrations
 */
class PkleindienstBlogSeriesMigration extends AbstractMigration
{
    const PLUGIN_NAME = 'PKleindienst.BlogSeries';

    private $migratedSeriesIds = [];

    /**
     * Migrate plugin data
     */
    protected function migratePlugin()
    {
        try {
            $this->migrateSeries();

            $this->migrateRelatedSeries();

            $this->migratePostSeries();
        } catch (Exception $exception) {
            $this->command->error($exception->getMessage());

            $this->rollbackSeries();
        }
    }

    /**
     * Rolls back the migration process
     */
    private function rollbackSeries()
    {
        if (!empty($this->migratedSeriesIds)) {
            $this->command->warn('Rolling back migration');

            BlogTaxonomySeries::whereIn('id', array_keys($this->migratedSeriesIds))->delete();

            $this->command->warn('Roll back complete');
        }
    }

    /**
     * Migrate general series data
     *
     * @throws NoDataException
     */
    private function migrateSeries()
    {
        $this->command->info('Migrating series');

        $series = $this->getSeries();

        foreach ($series as $seriesRecord) {
            $blogTaxonomySeries = new BlogTaxonomySeries();

            $blogTaxonomySeries->slug = $seriesRecord->slug;
            $blogTaxonomySeries->title = $seriesRecord->title;
            $blogTaxonomySeries->description = $seriesRecord->description;

            try {
                $blogTaxonomySeries->save();
            } catch (ModelException $exception) {
                $blogTaxonomySeries->slug .= '-migrated';
                $blogTaxonomySeries->title .= ' (migrated)';

                $blogTaxonomySeries->save();
            }

            $this->command->line(
                sprintf(
                    'Series "%s" => Blog Taxonomy Series "%s" (#%d)',
                    $seriesRecord->title,
                    $blogTaxonomySeries->title,
                    $blogTaxonomySeries->id
                )
            );

            $this->migratedSeriesIds[$blogTaxonomySeries->id] = $seriesRecord->id;
        }

        $this->command->info('All series have been migrated' . PHP_EOL);
    }

    /**
     * Migrate related series
     */
    private function migrateRelatedSeries()
    {
        if (empty($this->migratedSeriesIds) || !Schema::hasTable('pkleindienst_blogseries_related')) {
            return;
        }

        $this->command->info('Migrating related series');

        $relatedSeries = DB::table('pkleindienst_blogseries_related')->get();

        if (!count($relatedSeries)) {
            $this->command->warn('No related series found' . PHP_EOL);

            return;
        }

        $migratedSeries = array_flip($this->migratedSeriesIds);

        DB::transaction(function () use ($relatedSeries, $migratedSeries) {
            foreach ($relatedSeries as $relationRecord) {
                $seriesId = $migratedSeries[$relationRecord->series_id];
                $relatedSeriesId = $migratedSeries[$relationRecord->related_id];

                DB::table('ginopane_blogtaxonomy_related_series')->insert(
                    ['series_id' => $seriesId, 'related_series_id' => $relatedSeriesId]
                );

                $this->command->line(
                    sprintf(
                        'Relation "#%d" => "#%d" added',
                        $seriesId,
                        $relatedSeriesId
                    )
                );
            }
        });

        $this->command->info('Related series have been migrated' . PHP_EOL);
    }

    /**
     * Migrate series assigned to posts
     */
    private function migratePostSeries()
    {
        if (empty($this->migratedSeriesIds)) {
            return;
        }

        if (!$this->command->confirm(
            'Do you want to assign newly created series to posts (already assigned Blog Taxonomy series will be overwritten)'
        )) {
            return;
        }

        $this->command->info('Migrating series assigned to posts');

        $migratedSeries = array_flip($this->migratedSeriesIds);

        DB::transaction(function () use ($migratedSeries) {
            foreach ($migratedSeries as $oldSeriesId => $newSeriesId) {
                if (DB::table('rainlab_blog_posts')
                    ->where('series_id', $oldSeriesId)
                    ->update(['ginopane_blogtaxonomy_series_id' => $newSeriesId])
                ) {
                    $this->command->line(
                        sprintf(
                            'Series "#%d" has been assigned to a post',
                            $newSeriesId
                        )
                    );
                };
            }
        });

        $this->command->info('Migrated series has been assigned' . PHP_EOL);
    }

    /**
     * @return Collection|Series[]
     *
     * @throws NoDataException
     */
    private function getSeries()
    {
        $series = Series::all();

        if (!count($series)) {
            throw new NoDataException('No series records found');
        }

        $this->command->warn(sprintf('%d series found', count($series)));

        return $series;
    }
}