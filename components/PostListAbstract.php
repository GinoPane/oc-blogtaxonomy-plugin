<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Illuminate\Http\Response;
use Rainlab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PostListAbstract
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
abstract class PostListAbstract extends ComponentAbstract
{
    use TranslateArrayTrait;

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
        'title asc' => Plugin::LOCALIZATION_KEY . 'order_options.title_asc',
        'title desc' => Plugin::LOCALIZATION_KEY . 'order_options.title_desc',
        'created_at asc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_asc',
        'created_at desc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_desc',
        'updated_at asc' => Plugin::LOCALIZATION_KEY . 'order_options.updated_at_asc',
        'updated_at desc' => Plugin::LOCALIZATION_KEY . 'order_options.updated_at_desc',
        'published_at asc' => Plugin::LOCALIZATION_KEY . 'order_options.published_at_asc',
        'published_at desc' => Plugin::LOCALIZATION_KEY . 'order_options.published_at_desc',
        'random' => Plugin::LOCALIZATION_KEY . 'order_options.random'
    ];

    /**
     * Component Properties
     * @return array
     */
    public function defineProperties()
    {
        return [
            'orderBy' => [
                'title'       => 'rainlab.blog::lang.settings.posts_order',
                'description' => 'rainlab.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at asc',
                'showExternalParam' => false
            ],

            'page' => [
                'group'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.pagination_group',
                'title'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.page_parameter_title',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.page_parameter_description',
                'default'       => '{{ :page }}',
                'type'          => 'string',
            ],
            'resultsPerPage' => [
                'group'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.pagination_group',
                'title'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.pagination_per_page_title',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.pagination_per_page_description',
                'default'       => 10,
                'type'          => 'string',
                'validationPattern' => '^(0+)?[1-9]\d*$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.pagination_validation_message',
                'showExternalParam' => false,
            ],

            'postPage' => [
                'group'       => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'showExternalParam' => false,
            ],
            'categoryPage' => [
                'group'       => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'title'       => 'rainlab.blog::lang.settings.posts_category',
                'description' => 'rainlab.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'showExternalParam' => false,
            ]
        ];
    }

    /**
     * @see Post::$allowedSortingOptions
     *
     * @return string[]
     */
    public function getOrderByOptions()
    {
        $order = $this->translate(static::$postAllowedSortingOptions);

        asort($order);

        return $order;
    }

    /**
     * Query the tag and posts belonging to it
     */
    public function onRun()
    {
        if ($this->prepareContextItem() === null) {
            return Redirect::to($this->controller->pageUrl(Response::HTTP_NOT_FOUND));
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
        // Paginator settings
        $this->currentPage = (int)$this->property('page', 1) ?: (int)post('page');
        $this->resultsPerPage = (int)$this->property('resultsPerPage')
            ?: $this->defineProperties()['resultsPerPage']['default'];

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

        if (array_key_exists($this->orderBy, self::$postAllowedSortingOptions)) {
            if ($this->orderBy === 'random') {
                $query->inRandomOrder();
            } else {
                list($sortField, $sortDirection) = explode(' ', $this->orderBy);

                $query->orderBy($sortField, $sortDirection);
            }
        }

        $posts = $query->paginate($this->resultsPerPage, $this->currentPage);

        $this->setPostUrls($posts);

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
