<?php

namespace GinoPane\BlogTaxonomy\Components;

use DB;
use Cms\Classes\Page;
use RainLab\Blog\Models\Post;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Plugin;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RelatedPosts
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class RelatedPosts extends ComponentBase
{
    /**
     * @var Collection | array
     */
    public $posts = [];

    /**
     * Reference to the page name for linking to posts
     *
     * @var string
     */
    public $postPage;

    /**
     * Message to display when there are no posts
     *
     * @var string
     */
    public $noPostsMessage;

    /**
     * If the post list should be ordered by another attribute
     *
     * @var string
     */
    public $orderBy;

    /**
     * Limits the number of records to display
     *
     * @var int
     */
    public $limit;

    /**
     * Component Registration
     *
     * @return  array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.related_posts.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.related_posts.description'
        ];
    }

    /**
     * Component Properties
     *
     * @return  array
     */
    public function defineProperties()
    {
        return [
            'slug' => [
                'title'             => 'rainlab.blog::lang.settings.post_slug',
                'description'       => 'rainlab.blog::lang.settings.post_slug_description',
                'default'           => '{{ :slug }}',
                'type'              => 'string'
            ],

            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.tag_list.validation_message',
                'showExternalParam' => false
            ],

            'noPostsMessage' => [
                'title'        => 'rainlab.blog::lang.settings.posts_no_posts',
                'description'  => 'rainlab.blog::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'showExternalParam' => false
            ],

            'orderBy' => [
                'title'       => 'rainlab.blog::lang.settings.posts_order',
                'description' => 'rainlab.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at asc',
                'showExternalParam' => false
            ],

            'postPage' => [
                'title'       => 'Post page',
                'description' => 'Page to show linked posts',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
        ];
    }

    /**
     * @see PostListAbstract::$postAllowedSortingOptions
     *
     * @return mixed
     */
    public function getOrderByOptions()
    {
        return array_merge(
            [
                'relevance asc' => Plugin::LOCALIZATION_KEY . 'order_options.relevance_asc',
                'relevance desc' => Plugin::LOCALIZATION_KEY . 'order_options.relevance_desc'
            ],
            PostListAbstract::$postAllowedSortingOptions
        );
    }

    protected function prepareVars()
    {
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
        $this->orderBy = $this->page['orderBy'] = $this->property('orderBy');

        // Page links
        $this->postPage = $this->page['postPage' ] = $this->property('postPage');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Load post and start building query for related posts
     */
    public function onRun()
    {
        //Prepare vars
        $this->prepareVars();

        // Load the target post
        $post = Post::where('slug', $this->property('slug'))
            ->with('tags')
            ->first();

        // Abort if there is no source, or it has no tags
        if (!$post || (!$tagIds = $post->tags->lists('id')))
            return;

        // Start building our query for related posts
        $query = Post::isPublished()
            ->where('id', '<>', $post->id)
            ->whereHas('tags', function($tag) use ($tagIds) {
                $tag->whereIn('id', $tagIds);
            })
            ->with('tags');

        // Sort the related posts
        $subQuery = DB::raw('(
            select count(*)
            from `ginopane_blogtaxonomy_post_tag`
            where `ginopane_blogtaxonomy_post_tag`.`post_id` = `rainlab_blog_posts`.`id`
            and `ginopane_blogtaxonomy_post_tag`.`tag_id` in ('.implode(', ', $tagIds).')
        )');

        $key = $this->property('orderBy') ?: $subQuery;
        $query->orderBy($key, $this->property('direction'));

        // Limit the number of results
        if ($take = intval($this->property('results'))) {
            $query->take($take);
        }

        // Execute the query
        $posts = $query->get();

        /*
         * Add a "url" helper attribute for linking to each post
        */
        $posts->each(
            function($post)
            {
                $post->setUrl($this->postPage,$this->controller);
            }
        );

        $this->posts = $posts;

    }
}
