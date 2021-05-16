<?php

namespace GinoPane\BlogTaxonomy\Updates;

use DB;
use Schema;
use Carbon\Carbon;
use System\Classes\PluginManager;
use GinoPane\BlogTaxonomy\Models\PostType;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreatePostTypeTable
 *
 * @package GinoPane\BlogTaxonomy\Updates
 */
class CreatePostTypeTable extends Migration
{
    const GAME_REVIEW_TYPE = [
        [
            'name' => 'Release Date',
            'code' => 'release-date',
            'type' => 'datepicker',
            'datepicker_mode' => 'date'
        ],
        [
            'name' => 'Publisher',
            'code' => 'publisher',
            'type' => 'text'
        ],
        [
            'name' => 'Editor',
            'code' => 'editor',
            'type' => 'text'
        ],
        [
            'name' => 'Rating',
            'code' => 'rating',
            'type' => 'dropdown',
            'dropdown_options' => '1,2,3,4,5,6,7,8,9,10',
        ],
        [
            'name' => 'Pros',
            'code' => 'pros',
            'type' => 'textarea'
        ],
        [
            'name' => 'Cons',
            'code' => 'cons',
            'type' => 'textarea'
        ],
    ];

    /**
     * Execute migrations
     */
    public function up()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->createPostTypes();
        }
    }

    /**
     * Rollback migrations
     */
    public function down()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->dropPostTypes();
        }
    }

    /**
     * Rollback PostType migration
     */
    private function dropPostTypes()
    {
        if (Schema::hasColumn('rainlab_blog_posts', PostType::TABLE_NAME . '_id')) {
            Schema::table('rainlab_blog_posts', static function ($table) {
                $table->dropForeign([PostType::TABLE_NAME . '_id']);

                $table->dropColumn(PostType::TABLE_NAME . '_id');
            });
        }

        if (Schema::hasColumn('rainlab_blog_posts', PostType::TABLE_NAME . '_attributes')) {
            Schema::table('rainlab_blog_posts', static function ($table) {
                $table->dropColumn(PostType::TABLE_NAME . '_attributes');
            });
        }

        Schema::dropIfExists(PostType::TABLE_NAME);
    }

    /**
     * Create PostType table
     */
    private function createPostTypes()
    {
        if (!Schema::hasTable(PostType::TABLE_NAME)) {
            Schema::create(
                PostType::TABLE_NAME,
                static function ($table) {
                    $table->engine = 'InnoDB';

                    $table->increments('id');
                    $table->string('name')->unique();
                    $table->string('slug')->unique();
                    $table->text('description')->nullable();
                    $table->text('type_attributes')->nullable();
                    $table->timestamps();
                }
            );

            Schema::table('rainlab_blog_posts', function ($table) {
                $table->integer(PostType::TABLE_NAME . '_id')->unsigned()->nullable()->default(null);
                $table->foreign(PostType::TABLE_NAME . '_id')->references('id')->on(PostType::TABLE_NAME)->onDelete('cascade');

                $table->text(PostType::TABLE_NAME. '_attributes')->nullable();
            });

            DB::table(PostType::TABLE_NAME)->insert(
                [
                    'name' => 'Game Review',
                    'slug' => 'game-review',
                    'type_attributes' => json_encode(self::GAME_REVIEW_TYPE),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
