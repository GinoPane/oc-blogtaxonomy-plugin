<?php declare(strict_types=1);

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;
use GinoPane\BlogTaxonomy\Classes\ComponentAbstract;
use GinoPane\BlogTaxonomy\Classes\TranslateArrayTrait;
use GinoPane\BlogTaxonomy\Classes\PostListFiltersTrait;

/**
 * Class SeriesList
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesList extends ComponentAbstract
{
    const NAME = 'seriesList';

    use TranslateArrayTrait;
    use PostListFiltersTrait;

    /**
     * @var Series
     */
    public $series;

    /**
     * Reference to the page name for linking to series
     *
     * @var string
     */
    protected $seriesPage;

    /**
     * If the series list should be ordered by another attribute
     *
     * @var string
     */
    public $orderBy;

    /**
     * Whether display or not empty series
     *
     * @var bool
     */
    public $displayEmpty;

    /**
     * Limits the number of records to display
     *
     * @var int
     */
    public $limit;

    /**
     * Whether to query related posts or not
     *
     * @var bool
     */
    private $fetchPosts;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.series_list.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.series_list.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties(): array
    {
        return array_merge([
            'displayEmpty' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.display_empty_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.display_empty_description',
                'type'        =>    'checkbox',
                'default'     =>    false,
                'showExternalParam' => false
            ],
            'fetchPosts' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.series_list.fetch_posts_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.series_list.fetch_posts_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.series_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.series_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.series_list.limit_validation_message',
                'showExternalParam' => false
            ],
            'orderBy' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_description',
                'type'        =>    'dropdown',
                'default'     =>    'title asc',
                'showExternalParam' => false
            ],
            'seriesPage' => [
                'group'         =>  Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'title'         =>  Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_title',
                'description'   =>  Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_description',
                'type'          =>  'dropdown',
                'default'       =>  'blog/series',
                'showExternalParam' => false
            ],
        ], $this->getPostFilterProperties());
    }

    /**
     * @return mixed
     */
    public function getSeriesPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return string[]
     */
    public function getOrderByOptions(): array
    {
        $order = $this->translate(Series::$sortingOptions);

        asort($order);

        return $order;
    }

    /**
     * Prepare and return a series list
     *
     * @return mixed
     */
    public function onRun()
    {
        $this->prepareVars();

        // Exceptions
        $this->populateFilters();

        $this->series = $this->listSeries();
    }

    /**
     * Get Series
     *
     * @return mixed
     */
    protected function listSeries()
    {
        $series = Series::listFrontend([
            'sort' => $this->orderBy,
            'displayEmpty' => $this->displayEmpty,
            'limit' => $this->limit,
            'fetchPosts' => $this->fetchPosts,
            'exceptPosts' => $this->exceptPosts,
            'includeCategories' => $this->includeCategories,
            'exceptCategories' => $this->exceptCategories
        ]);

        $this->handleUrls($series);

        return $series;
    }

    /**
     * @param $series
     */
    protected function handleUrls($series)
    {
        $seriesComponent = $this->getComponent(SeriesPosts::NAME, $this->seriesPage);

        $this->setUrls(
            $series,
            $this->seriesPage,
            $this->controller,
            [
                'series' => $this->urlProperty($seriesComponent, 'series')
            ]
        );
    }

    private function prepareVars(): void
    {
        $this->seriesPage = $this->getProperty('seriesPage');
        $this->orderBy = $this->getProperty('orderBy');
        $this->displayEmpty = (bool)$this->getProperty('displayEmpty');
        $this->fetchPosts = (bool)$this->getProperty('fetchPosts');
        $this->limit = $this->getProperty('limit');
    }
}
