<?php

namespace GinoPane\BlogTaxonomy\Components;

use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;

/**
 * Class SeriesPosts
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesPosts extends PostListAbstract
{
    /**
     * @var Series
     */
    public $series;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.series_posts.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.series_posts.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $properties = [
                'series' => [
                    'title'       => 'Series Slug',
                    'description' => 'Look up the series using the supplied slug value.',
                    'type'        => 'string',
                    'default'     => '{{ :slug }}',
                ],
            ] + parent::defineProperties();

        return $properties;
    }

    /**
     * Prepare variables
     */
    protected function prepareContextItem()
    {
        // load series
        $this->series = Series::where('slug', $this->property('series'))->first();

        return $this->series;
    }

    /**
     * @return mixed
     */
    protected function getPostsQuery()
    {
        $query = Post::whereHas('series', function ($query) {
            $query->where('slug', $this->series->slug);
        })->isPublished();

        return $query;
    }
}
