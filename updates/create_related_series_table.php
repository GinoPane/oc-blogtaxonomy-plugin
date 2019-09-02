<?php

namespace GinoPane\BlogTaxonomy\Updates;

use Schema;
use System\Classes\PluginManager;
use GinoPane\BlogTaxonomy\Models\Series;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateRelatedSeriesTable
 *
 * @package GinoPane\BlogTaxonomy\Updates
 */
class CreateRelatedSeriesTable extends Migration
{
    /**
     * Execute migrations
     */
    public function up()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->createRelation();
        }
    }

    /**
     * Rollback migrations
     */
    public function down()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->dropRelation();
        }
    }

    /**
     * Rollback relation migration
     */
    private function dropRelation()
    {
        Schema::dropIfExists(Series::RELATED_SERIES_TABLE_NAME);
    }

    /**
     * Create relation table
     */
    private function createRelation()
    {
        if (!Schema::hasTable(Series::RELATED_SERIES_TABLE_NAME)) {
            Schema::create(
                Series::RELATED_SERIES_TABLE_NAME,
                static function ($table) {
                    $table->engine = 'InnoDB';

                    $table->integer('series_id')->unsigned();
                    $table->integer('related_series_id')->unsigned();
                    $table->index(['series_id', 'related_series_id'], 'related_series_index');

                    $table
                        ->foreign('series_id', 'Series reference')
                        ->references('id')
                        ->on(Series::TABLE_NAME)
                        ->onDelete('cascade');

                    $table
                        ->foreign('related_series_id', 'Related series reference')
                        ->references('id')
                        ->on(Series::TABLE_NAME)
                        ->onDelete('cascade');
                }
            );
        }
    }
}
