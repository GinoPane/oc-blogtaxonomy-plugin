<?php

namespace GinoPane\BlogTaxonomy\Updates;

use Schema;
use RainLab\Blog\Models\Post;
use System\Classes\PluginManager;
use Illuminate\Support\Facades\DB;
use GinoPane\BlogTaxonomy\Models\Tag;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreatePolymorphicTagTable
 *
 * @package GinoPane\BlogTaxonomy\Updates
 */
class CreatePolymorphicTagTable extends Migration
{
    /**
     * Execute migrations
     */
    public function up()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->createTaggablesTable();
        }
    }

    /**
     * Rollback migrations
     */
    public function down()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            $this->dropTaggablesTable();
        }
    }

    /**
     * Rollback Taggables migration
     */
    private function dropTaggablesTable()
    {
        Schema::dropIfExists(Tag::PIVOT_TABLE);
    }

    /**
     * Create Taggables table
     */
    private function createTaggablesTable()
    {
        if (!Schema::hasTable(Tag::PIVOT_TABLE)) {
            $pivotColumnId = Tag::PIVOT_COLUMN . '_id';
            $pivotColumnType = Tag::PIVOT_COLUMN . '_type';

            Schema::create(
                Tag::PIVOT_TABLE,
                static function ($table) use ($pivotColumnId, $pivotColumnType) {
                    $table->engine = 'InnoDB';

                    $table->integer('tag_id')->unsigned()->nullable()->default(null);
                    $table->integer($pivotColumnId)->unsigned()->nullable()->default(null);
                    $table->string($pivotColumnType);

                    $table->index(
                        ['tag_id', Tag::PIVOT_COLUMN . '_id', Tag::PIVOT_COLUMN . '_type'],
                        'ginopane_blogtaxonomy_taggable_index'
                    );

                    $table
                        ->foreign('tag_id')
                        ->references('id')
                        ->on(Tag::TABLE_NAME)
                        ->onDelete('cascade');
                }
            );

            // Current tag relations
            $savedTags = DB::table(Tag::CROSS_REFERENCE_TABLE_NAME)->select('tag_id', 'post_id')->get()->toArray();

            $savedTags = array_map(static function($savedTag) use ($pivotColumnId, $pivotColumnType) {
                return [
                    'tag_id' => $savedTag->tag_id,
                    $pivotColumnId => $savedTag->post_id,
                    $pivotColumnType => Post::class
                ];
            }, $savedTags);

            DB::table(Tag::PIVOT_TABLE)->insert($savedTags);
        }
    }
}
