<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Models\Series;
use RainLab\Blog\Models\Post as BlogPost;

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
     * Reference to the page name for linking to series.
     * @var string
     */
    public $seriesPage;

    /**
     * If the series list should be ordered by another attribute.
     * @var string
     */
    public $sortOrder;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'Blog Series List',
            'description' => 'Displays a list of blog series on the page.'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'displayEmpty' => [
                'title'       => 'Display empty series',
                'description' => 'Show series that do not have any posts.',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'seriesPage' => [
                'title'       => 'Series Page',
                'description' => 'The page where the single series are displayed.',
                'type'        => 'dropdown',
                'default'     => 'blog/series'
            ],
            'sortOrder' => [
                'title'       => 'Order',
                'description' => 'Attribute on which the items should be ordered',
                'type'        => 'dropdown',
                'default'     => 'title asc'
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
     * @return mixed
     */
    public function onRun()
    {
        // load series
        $this->seriesPage = $this->property('seriesPage');
        $this->sortOrder = $this->property('sortOrder');
        $this->series = $this->listSeries();
    }

    /**
     * Get Series
     * @return mixed
     */
    protected function listSeries()
    {
        // get series
        $series = Series::listFrontend([
            'sort' => $this->property('sortOrder')
        ]);

        // Add a "url" helper attribute for linking to each post and category
        if ($series && !$series->isEmpty()) {
            foreach ($series as $key => $item) {
                // remove empty series
                if (!$this->property('displayEmpty') && $item->postCount === 0) {
                    $series->forget($key);
                    continue;
                }

                $item->setUrl($this->seriesPage, $this->controller);
            }
        }

        return $series;
    }
}
