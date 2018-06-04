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
    public $sortOrder;

    /**
     * Whether display or not empty series
     *
     * @var bool
     */
    public $displayEmpty;

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
            'sortOrder' => [
                'title'       =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_title',
                'description' =>    Plugin::LOCALIZATION_KEY . 'components.series_list.order_description',
                'type'        =>    'dropdown',
                'default'     =>    'title asc'
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
     * @see BlogPost::$allowedSortingOptions
     *
     * @return mixed
     */
    public function getSortOrderOptions()
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
        // load series
        $this->seriesPage = $this->property('seriesPage', '');
        $this->sortOrder = $this->property('sortOrder', 'title asc');
        $this->displayEmpty = $this->property('displayEmpty', false);

        $this->series = $this->listSeries();
    }

    /**
     * Get Series
     *
     * @return mixed
     */
    protected function listSeries()
    {
        // get series
        $series = Series::listFrontend([
            'sort' => $this->sortOrder,
            'displayEmpty' => $this->property('displayEmpty')
        ]);

        // Add a "url" helper attribute for linking to each post and category
        if ($series) {
            foreach ($series as $item) {
                $item->setUrl($this->seriesPage, $this->controller);
            }
        }

        return $series;
    }
}
