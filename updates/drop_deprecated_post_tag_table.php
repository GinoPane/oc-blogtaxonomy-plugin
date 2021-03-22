<?php

namespace GinoPane\BlogTaxonomy\Updates;

use Schema;
use GinoPane\BlogTaxonomy\Models\Tag;
use October\Rain\Database\Updates\Migration;

/**
 * Class DropDeprecatedPostTagTable
 *
 * @package GinoPane\BlogTaxonomy\Updates
 */
class DropDeprecatedPostTagTable extends Migration
{
    /**
     * Execute migrations
     */
    public function up()
    {
        Schema::dropIfExists('ginopane_blogtaxonomy_post_tag');
    }

    /**
     * Rollback migrations
     */
    public function down()
    {
        if (!Schema::hasTable('ginopane_blogtaxonomy_post_tag')) {
            Schema::create(
                'ginopane_blogtaxonomy_post_tag',
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
}
