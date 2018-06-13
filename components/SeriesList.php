<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;

/**
 * Class SeriesList
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesList extends ComponentBase
{
    use UrlHelperTrait;

    /**
     * @var Series
     */
    public $series;

    /**
     * Reference to the page name for linking to series
     *
     * @var string
     */
    public $seriesPage;

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
    public function defineProperties()
    {
        return [
            'displayEmpty' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.display_empty_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.display_empty_description',
                'type'        =>    'checkbox',
                'default'     =>    false
            ],
            'seriesPage' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_description',
                'type'        =>    'dropdown',
                'default'     =>    'blog/series'
            ],
            'orderBy' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_description',
                'type'        =>    'dropdown',
                'default'     =>    'title asc'
            ],
            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.series_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.series_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.series_list.validation_message'
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getSeriesPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return mixed
     */
    public function getOrderByOptions()
    {
        return Series::$sortingOptions;
    }

    /**
     * Prepare and return a series list
     *
     * @return mixed
     */
    public function onRun()
    {
        $this->seriesPage = $this->property('seriesPage', '');
        $this->orderBy = $this->property('orderBy', 'title asc');
        $this->displayEmpty = $this->property('displayEmpty', false);
        $this->limit = $this->property('limit', 0);

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
            'limit' => $this->limit
        ]);

        $this->setUrls($series, $this->seriesPage, $this->controller);

        return $series;
    }
}
