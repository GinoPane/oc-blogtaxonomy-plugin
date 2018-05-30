<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Models\Series;
use RainLab\Blog\Models\Post as BlogPost;

/**
 * Class SeriesNavigation
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesNavigation extends ComponentBase
{
    /**
     * @var Series
     */
    public $series;

    /**
     * Series slug
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
            'name'        => 'Post Navigation',
            'description' => 'Displays a navigation for the current posts series.'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'rainlab.blog::lang.settings.post_slug',
                'description' => 'rainlab.blog::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'smallNav' => [
                'title'       => 'Small Navigation',
                'description' => 'Display a small "Previous/Next Navigation" instead of a full post list',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
            'seriesPage' => [
                'title'       => 'Series Page',
                'description' => 'The page where the single series are displayed.',
                'type'        => 'dropdown',
                'default'     => 'blog/series',
                'group'       => 'Links',
            ],
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
        $this->slug       = $this->page[ 'slug' ] = $this->property('slug');
        $this->smallNav   = $this->page[ 'smallNav' ] = $this->property('smallNav');
        $this->series     = $this->page[ 'series' ] = $this->listSeries();
    }

    /**
     * Get Series
     * @return mixed
     */
    protected function listSeries()
    {
        $series = BlogPost::with('series.posts')->isPublished()->where('slug', $this->slug)->first();

        // Add a "url" helper attribute for linking to each post and series
        if ($series && !is_null($series->series)) {
            $series = $series->series;

            $series->setUrl($this->seriesPage, $this->controller);

            $series->posts->each(function ($post) {
                $post->setUrl($this->postPage, $this->controller);
            });
        }

        return $series;
    }
}
