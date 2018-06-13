<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Illuminate\Http\Response;
use Rainlab\Blog\Models\Post;
use Cms\Classes\ComponentBase;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PostListAbstract
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
abstract class PostListAbstract extends ComponentBase
{
    use UrlHelperTrait;

    /**
     * @var Collection | array
     */
    public $posts = [];

    /**
     * @var integer             The current page
     */
    public $currentPage;

    /**
     * @var integer             The number of results per page
     */
    public $resultsPerPage;

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
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $postAllowedSortingOptions = [
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'published_at asc' => 'Published (ascending)',
        'published_at desc' => 'Published (descending)',
        'random' => 'Random'
    ];

    /**
     * Component Properties
     * @return array
     */
    public function defineProperties()
    {
        return [
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

            'page' => [
                'title'         => 'Page',
                'description'   => 'The URL parameter defining the page number.',
                'default'       => '{{ :page }}',
                'type'          => 'string',
                'group'         => 'Pagination',
            ],
            'resultsPerPage' => [
                'title'         => 'Results',
                'description'   => 'The number of posts to display per page.',
                'default'       => 10,
                'type'          => 'string',
                'validationPattern' => '^(0+)?[1-9]\d*$',
                'validationMessage' => 'Results per page must be a positive whole number.',
                'showExternalParam' => false,
                'group'         => 'Pagination',
            ],

            'postPage' => [
                'title'       => 'Post page',
                'description' => 'Page to show linked posts',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
            'categoryPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_category',
                'description' => 'rainlab.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ]
        ];
    }

    /**
     * @see Post::$allowedSortingOptions
     *
     * @return mixed
     */
    public function getOrderByOptions()
    {
        return static::$postAllowedSortingOptions;
    }

    /**
     * Query the tag and posts belonging to it
     */
    public function onRun()
    {
        if (is_null($this->prepareContextItem())) {
            return $this->controller->run(Response::HTTP_NOT_FOUND);
        }

        $this->prepareVars();

        $this->listPosts();
    }

    /**
     * Prepare variables
     */
    abstract protected function prepareContextItem();

    /**
     * Prepare variables
     */
    protected function prepareVars()
    {
        $this->currentPage = intval($this->property('page', 1)) ?: intval(post('page'));
        $this->resultsPerPage = intval($this->property('resultsPerPage'))
            ?: $this->defineProperties()['resultsPerPage']['default'];

        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
        $this->orderBy = $this->page['orderBy'] = $this->property('orderBy');

        // Page links
        $this->postPage = $this->page['postPage' ] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    /**
     * Load a list of posts
     */
    public function listPosts()
    {
        $query = $this->getPostsQuery();

        if (in_array($this->orderBy, array_keys(self::$postAllowedSortingOptions))) {
            if ($this->orderBy == 'random') {
                $query->inRandomOrder();
            } else {
                list($sortField, $sortDirection) = explode(' ', $this->orderBy);

                $query->orderBy($sortField, $sortDirection);
            }
        }

        $posts = $query->paginate($this->resultsPerPage, $this->currentPage);

        // Add a "url" helper attribute for linking to each post and category
        if ($posts && $posts->count()) {
            $posts->each([$this, 'setPostUrls']);
        }

        $this->posts = $posts;
    }

    /**
     * @return mixed
     */
    abstract protected function getPostsQuery();

    /**
     * @return mixed
     */
    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return mixed
     */
    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }
}
