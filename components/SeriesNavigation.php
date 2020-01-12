<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;
use GinoPane\BlogTaxonomy\Models\ModelAbstract;
use GinoPane\BlogTaxonomy\Classes\ComponentAbstract;

/**
 * Class SeriesNavigation
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesNavigation extends ComponentAbstract
{
    const NAME = 'seriesNavigation';

    /**
     * @var Series
     */
    public $series;

    /**
     * Post slug
     *
     * @var string
     */
    public $slug;

    /**
     * @var int
     */
    public $smallNav;

    /**
     * Reference to the page name for linking to posts
     *
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to series
     *
     * @var string
     */
    public $seriesPage;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.series_navigation.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.series_navigation.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       =>  Plugin::LOCALIZATION_KEY . 'components.series_navigation.post_slug_title',
                'description' =>  Plugin::LOCALIZATION_KEY . 'components.series_navigation.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'seriesPage' => [
                'title'       => Plugin::LOCALIZATION_KEY . 'components.series_navigation.series_page_title',
                'description' => Plugin::LOCALIZATION_KEY . 'components.series_navigation.series_page_description',
                'type'        => 'dropdown',
                'default'     => 'blog/series',
                'group'       => Plugin::LOCALIZATION_KEY . 'components.series_navigation.links_group',
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => Plugin::LOCALIZATION_KEY . 'components.series_navigation.links_group',
            ]
        ];
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
    public function getSeriesPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @return void
     */
    public function onRun()
    {
        $this->postPage   = $this->property('postPage');
        $this->seriesPage = $this->property('seriesPage');
        $this->slug       = $this->page['slug'] = $this->property('slug');
        $this->series     = $this->page['series'] = $this->listSeries();
    }

    /**
     * Get Series
     *
     * @return mixed
     */
    protected function listSeries()
    {
        $series = Series::whereHas(
            'posts',
            function($query) {
                ModelAbstract::whereTranslatableProperty($query, 'slug', $this->property('slug'));
            }
        )->with(
            [
                'posts' => static function($query) {
                    $query->isPublished();
                }
            ]
        )->first();

        if ($series !== null) {
            $seriesComponent = $this->getComponent(SeriesPosts::NAME, $this->seriesPage);

            $series->setUrl($this->seriesPage, $this->controller, [
                'series' => $this->urlProperty($seriesComponent, 'series')
            ]);

            $this->setPostUrls($series->posts);
        }

        return $series;
    }
}
