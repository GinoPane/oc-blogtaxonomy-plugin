<?php

namespace GinoPane\BlogTaxonomy\Updates;

use GinoPane\BlogTaxonomy\Models\Series;
use Schema;
use System\Classes\PluginManager;
use GinoPane\BlogTaxonomy\Models\Tag;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateTaxonomiesTables
 *
 * @package GinoPane\BlogTaxonomy\Updates
 */
class CreateTaxonomiesTables extends Migration
{
    /**
     * Execute migrations
     */
    public function up()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->createTags();

            $this->createSeries();
        }
    }

    /**
     * Rollback migrations
     */
    public function down()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->dropTags();

            $this->dropSeries();
        }
    }

    /**
     * Rollback Tags migration
     */
    private function dropTags()
    {
        Schema::dropIfExists(Tag::CROSS_REFERENCE_TABLE_NAME);
        Schema::dropIfExists(Tag::TABLE_NAME);
    }

    /**
     * Rollback Series migration
     */
    private function dropSeries()
    {
        Schema::table('rainlab_blog_posts', static function ($table) {
            $table->dropForeign([Series::TABLE_NAME . '_id']);
            $table->dropColumn(Series::TABLE_NAME . '_id');
        });

        Schema::dropIfExists(Series::TABLE_NAME);
    }

    /**
     * Create Tags table
     */
    private function createTags()
    {
        if (!Schema::hasTable(Tag::TABLE_NAME)) {
            Schema::create(
                Tag::TABLE_NAME,
                static function ($table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('name')->unique();
                    $table->string('slug')->unique();
                    $table->timestamps();
                }
            );
        }

        if (!Schema::hasTable(Tag::CROSS_REFERENCE_TABLE_NAME)) {
            Schema::create(
                Tag::CROSS_REFERENCE_TABLE_NAME,
                static function ($table) {
                    $table->engine = 'InnoDB';

                    $table->integer('tag_id')->unsigned()->nullable()->default(null);
                    $table->integer('post_id')->unsigned()->nullable()->default(null);
                    $table->index(['tag_id', 'post_id']);
                    $table->foreign('tag_id')->references('id')->on(Tag::TABLE_NAME)->onDelete('cascade');
                    $table->foreign('post_id')->references('id')->on('rainlab_blog_posts')->onDelete('cascade');
                }
            );
        }
    }

    /**
     * Create Series table
     */
    private function createSeries()
    {
        if (!Schema::hasTable(Series::TABLE_NAME)) {
            Schema::create(
                Series::TABLE_NAME,
                static function ($table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('title')->unique();
                    $table->string('slug')->unique();
                    $table->string('description')->nullable();
                    $table->timestamps();
                }
            );

            Schema::table('rainlab_blog_posts', function ($table) {
                $table->integer(Series::TABLE_NAME . '_id')->unsigned()->nullable()->default(null);
                $table->foreign(Series::TABLE_NAME . '_id')->references('id')->on(Series::TABLE_NAME)->onDelete('cascade');
            });
        }
    }
}
