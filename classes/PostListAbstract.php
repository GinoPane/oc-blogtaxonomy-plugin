<?php declare(strict_types=1);

namespace GinoPane\BlogTaxonomy\Classes;

use Cms\Classes\Page;
use Illuminate\Http\Response;
use Rainlab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use October\Rain\Database\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PostListAbstract
 *
 * @package GinoPane\BlogTaxonomy\Classes
 */
abstract class PostListAbstract extends ComponentAbstract
{
    use TranslateArrayTrait;
    use PostListFiltersTrait;

    /**
     * @var Collection | array
     */
    public $posts = [];

    /**
     * Parameter to use for the page number
     *
     * @var string
     */
    public $pageParam;

    /**
     * @var integer The current page
     */
    public $currentPage;

    /**
     * @var integer The number of results per page
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
     * Component properties
     *
     * @return array
     */
    public function defineProperties(): array
    {
        $properties = [
            'orderBy' => [
                'title'       => 'rainlab.blog::lang.settings.posts_order',
                'description' => 'rainlab.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at asc',
                'showExternalParam' => false
            ]
        ];

        return array_merge(
            $properties,
            $this->getPaginationProperties(),
            $this->getPageLinkProperties(),
            $this->getPostFilterProperties()
        );
    }

    /**
     * @see Post::$allowedSortingOptions
     *
     * @return string[]
     */
    public function getOrderByOptions(): array
    {
        $order = $this->translate(static::$postAllowedSortingOptions);

        asort($order);

        return $order;
    }

    /**
     * Query the item and posts belonging to it
     *
     * @return void|RedirectResponse
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
     * Load a list of posts
     */
    public function listPosts()
    {
        $query = $this->getPostsQuery();

        $this->handlePostFilters($query);

        $this->handleOrder($query);

        $posts = $query->paginate($this->resultsPerPage, $this->currentPage);

        $this->setPostUrls($posts);

        $this->posts = $posts;
    }

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

    /**
     * Prepare main context item
     */
    abstract protected function prepareContextItem();

    /**
     * @return mixed
     */
    abstract protected function getPostsQuery();

    /**
     * Prepare variables
     */
    protected function prepareVars()
    {
        // Paginator settings
        $this->populatePagination();
        // Page links
        $this->populateLinks();
        // Exceptions
        $this->populateFilters();

        $this->orderBy = $this->property('orderBy');
    }

    /**
     * Properties for pagination handling
     *
     * @return array
     */
    private function getPaginationProperties(): array
    {
        return [
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
            ]
        ];
    }

    /**
     * Properties for proper links handling
     *
     * @return array
     */
    private function getPageLinkProperties(): array
    {
        return [
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
            ],
        ];
    }

    /**
     * @return void
     */
    private function populatePagination()
    {
        $this->pageParam = $this->paramName('page');
        $this->currentPage = (int)$this->property('page', 1) ?: (int)post('page');
        $this->resultsPerPage = (int)$this->property('resultsPerPage')
            ?: $this->defineProperties()['resultsPerPage']['default'];
    }

    /**
     * @return void
     */
    private function populateLinks()
    {
        $this->postPage = $this->property('postPage');
        $this->categoryPage = $this->property('categoryPage');
    }

    /**
     * @param $query
     */
    private function handleOrder(Builder $query)
    {
        if (array_key_exists($this->orderBy, self::$postAllowedSortingOptions)) {
            if ($this->orderBy === 'random') {
                $query->inRandomOrder();
            } else {
                list($sortField, $sortDirection) = explode(' ', $this->orderBy);

                $query->orderBy($sortField, $sortDirection);
            }
        }
    }
}
