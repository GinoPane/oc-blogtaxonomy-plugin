<?php declare(strict_types=1);

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;
use October\Rain\Database\Collection;
use GinoPane\BlogTaxonomy\Classes\ComponentAbstract;
use GinoPane\BlogTaxonomy\Classes\TranslateArrayTrait;
use GinoPane\BlogTaxonomy\Classes\PostListFiltersTrait;

/**
 * Class TagList
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class TagList extends ComponentAbstract
{
    const NAME = 'tagList';

    use TranslateArrayTrait;
    use PostListFiltersTrait;

    /**
     * @var Collection | array
     */
    public $tags = [];

    /**
     * Reference to the page name for linking to tag page
     *
     * @var string
     */
    private $tagPage;

    /**
     * Reference to the page name for linking to all tags page
     *
     * @var string
     */
    private $tagsPage;

    /**
     * URL to the page where all tags are listed
     *
     * @var string
     */
    public $tagsPageUrl;

    /**
     * If the tag list should be ordered by another attribute
     *
     * @var string
     */
    private $orderBy;

    /**
     * Whether display empty tags or not
     *
     * @var bool
     */
    private $displayEmpty;

    /**
     * Filter tags for the post defined by slug
     *
     * @var string
     */
    private $postSlug;

    /**
     * Whether to query related posts or not
     *
     * @var bool
     */
    private $fetchPosts;

    /**
     * Limits the number of records to display
     *
     * @var int
     */
    public $limit;

    /**
     * Count whether overall amount of tags or amount of tags under "limit" only
     *
     * @var bool
     */
    private $exposeTotalCount;

    /**
     * Fetches post count of posts which belong to series tagged with the tag
     *
     * @var bool
     */
    private $fetchSeriesPostCount;

    /**
     * Whether include or not tags which belong to the post
     *
     * @var bool
     */
    private $includeSeriesTags;

    /**
     * Contains either the total number of tags in the list limited by "limit", or
     * the total number of tags which may be more than the limit if "exposeTotalCount" is used
     *
     * @var int
     */
    public $totalCount;

    /**
     * Whether enable tag filter input or not
     *
     * @var bool
     */
    private $enableTagFilter;

    /**
     * Whether include tag filter input or not
     *
     * @var bool
     */
    public $tagFilterEnabled;

    /**
     * Allows to output some debug information
     *
     * @var bool
     */
    public $debugOutput;

    /**
     * Tag filter options
     */
    const TAG_FILTER_NEVER = 'never';
    const TAG_FILTER_ALWAYS = 'always';
    const TAG_FILTER_ON_OVERFLOW = 'on_overflow';

    /**
     * Translations for tag filter options
     *
     * @var array
     */
    private static $enableTagFilterOptions = [
        self::TAG_FILTER_NEVER => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_filter_options.never',
        self::TAG_FILTER_ALWAYS => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_filter_options.always',
        self::TAG_FILTER_ON_OVERFLOW => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_filter_options.on_overflow'
    ];

    /**
     * Component Registration
     *
     * @return  array
     */
    public function componentDetails(): array
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.tag_list.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.tag_list.description'
        ];
    }

    /**
     * Component Properties
     *
     * @return  array
     */
    public function defineProperties(): array
    {
        return array_merge([

            //General properties
            'displayEmpty' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'fetchPosts' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.fetch_posts_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.fetch_posts_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'orderBy' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_description',
                'type'              => 'dropdown',
                'default'           => 'name asc',
                'showExternalParam' => false
            ],
            'postSlug' => [
                'title'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.post_slug_title',
                'description' => Plugin::LOCALIZATION_KEY . 'components.tag_list.post_slug_description',
                'type'        => 'string'
            ],
            'includeSeriesTags' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.include_series_tags_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.include_series_tags_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'fetchSeriesPostCount' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.fetch_series_post_count_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.fetch_series_post_count_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],

            //Limit properties
            'limit' => [
                'group'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_group',
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_validation_message',
                'showExternalParam' => false
            ],
            'exposeTotalCount' => [
                'group'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_group',
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.expose_total_count_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.expose_total_count_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'enableTagFilter' => [
                'group'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_group',
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_filter_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_filter_description',
                'type'              => 'dropdown',
                'default'           => self::TAG_FILTER_NEVER,
                'showExternalParam' => false
            ],

            //Links
            'tagPage' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_page_title',
                'group'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_list.tag_page_description',
                'type'          => 'dropdown',
                'showExternalParam' => false
            ],

            'tagsPage' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_title',
                'group'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_description',
                'type'          => 'dropdown',
                'showExternalParam' => false
            ],

            //Special
            'debugOutput' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.debug_output_title',
                'group'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.special_group',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_list.debug_output_description',
                'type'          => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
        ], $this->getPostFilterProperties());
    }

    /**
     * @return mixed
     */
    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return mixed
     */
    public function getTagsPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return array
     */
    public function getOrderByOptions(): array
    {
        $order = $this->translate(Tag::$sortingOptions);

        asort($order);

        return $order;
    }

    /**
     * @return array
     */
    public function getEnableTagFilterOptions(): array
    {
        return $this->translate(self::$enableTagFilterOptions);
    }

    /**
     * Prepare and return a tag list
     *
     * @return void
     */
    public function onRun()
    {
        $this->prepareVars();

        // Exceptions
        $this->populateFilters();

        $this->tags = $this->listTags();
    }

    /**
     * @return Collection
     */
    private function listTags(): Collection
    {
        $tags = Tag::listFrontend([
            'sort' => $this->orderBy,
            'displayEmpty' => $this->displayEmpty,
            'limit' => $this->limit,
            'post' => $this->postSlug,
            'fetchPosts' => $this->fetchPosts,
            'exceptPosts' => $this->exceptPosts,
            'includeCategories' => $this->includeCategories,
            'exceptCategories' => $this->exceptCategories,
            'fetchSeriesPostCount' => $this->fetchSeriesPostCount,
            'includeSeriesTags' => $this->includeSeriesTags
        ]);

        if ($this->fetchSeriesPostCount) {
            foreach ($tags as $tag) {
                foreach ($tag->series as $tagSeries) {
                    $tag->series_posts_count += $tagSeries->posts_count;
                }
            }
        }

        $this->handleCount($tags);
        $this->handleTagUrls($tags);
        $this->handleTagFilter();

        return $tags;
    }

    /**
     * @param $tags
     */
    private function handleTagUrls($tags)
    {
        $tagComponent = $this->getComponent(TagPosts::NAME, $this->tagPage);

        $this->tagsPageUrl = $this->controller->pageUrl($this->tagsPage);

        $this->setUrls($tags, $this->tagPage, $this->controller, ['tag' => $this->urlProperty($tagComponent, 'tag')]);
    }

    /**
     * @param $tags
     */
    private function handleCount($tags)
    {
        $this->totalCount = $tags->count();

        if ($this->exposeTotalCount && ($this->limit > 0) && ($this->totalCount === $this->limit)) {
            $this->totalCount = Tag::listFrontend([
                'displayEmpty' => $this->displayEmpty,
                'post' => $this->postSlug
            ])->count();
        }
    }

    /**
     * Enable tag filter input if required
     */
    private function handleTagFilter()
    {
        $this->tagFilterEnabled = false;

        switch ($this->enableTagFilter) {
            // fall-through is intentional here, setting of tagFilterEnabled to true is valid for both cases
            case self::TAG_FILTER_ON_OVERFLOW:
                if (!($this->totalCount > $this->limit)) {
                    break;
                }
            case self::TAG_FILTER_ALWAYS:
                $this->tagFilterEnabled = true;
        }

        if ($this->tagFilterEnabled) {
            $this->addJs('/plugins/' . Plugin::DIRECTORY_KEY . '/assets/js/jquery.mark.min.js');
        }
    }

    private function prepareVars()
    {
        $this->tagPage = (string) $this->getProperty('tagPage');
        $this->tagsPage = (string) $this->getProperty('tagsPage');

        $this->orderBy = $this->getProperty('orderBy');
        $this->postSlug = $this->getProperty('postSlug');
        $this->fetchPosts = (bool) $this->getProperty('fetchPosts');
        $this->displayEmpty = (bool) $this->getProperty('displayEmpty');
        $this->limit = (int) $this->getProperty('limit');
        $this->exposeTotalCount = (bool) $this->getProperty('exposeTotalCount');
        $this->enableTagFilter = (string) $this->getProperty('enableTagFilter');
        $this->includeSeriesTags = (bool) $this->getProperty('includeSeriesTags');
        $this->fetchSeriesPostCount = (bool) $this->getProperty('fetchSeriesPostCount');
        $this->debugOutput = (bool) $this->getProperty('debugOutput');
    }
}
