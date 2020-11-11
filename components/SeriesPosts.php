<?php

namespace GinoPane\BlogTaxonomy\Components;

use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;
use GinoPane\BlogTaxonomy\Classes\PostListAbstract;

/**
 * Class SeriesPosts
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesPosts extends PostListAbstract
{
    // Param name to be used in URLs: ":series"
    const URL_PARAM_NAME = 'series';

    const NAME = 'postsInSeries';

    /**
     * @var Series
     */
    public $series;

    /**
     * @var bool
     */
    protected $includeTaggedPosts = false;

    /**
     * @return array
     */
    public function componentDetails(): array
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.series_posts.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.series_posts.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties(): array
    {
        return [
                'series' => [
                    'title'       => Plugin::LOCALIZATION_KEY . 'components.series_posts.series_title',
                    'description' => Plugin::LOCALIZATION_KEY . 'components.series_posts.series_description',
                    'type'        => 'string',
                    'default'     => '{{ :series }}',
                ],
                'includeTaggedPosts' => [
                    'title'         => Plugin::LOCALIZATION_KEY . 'components.series_posts.include_tagged_posts_title',
                    'description'   => Plugin::LOCALIZATION_KEY . 'components.series_posts.include_tagged_posts_description',
                    'default'       => false,
                    'type'          => 'checkbox',
                    'showExternalParam' => false
                ]
            ] + parent::defineProperties();
    }

    /**
     * @inheritDoc
     */
    protected function prepareVars()
    {
        parent::prepareVars();

        $this->includeTaggedPosts = $this->property('includeTaggedPosts', false);
    }

    /**
     * @inheritDoc
     */
    protected function prepareContextItem()
    {
        // load series
        $this->series = Series::whereTranslatable('slug', $this->property('series'))->first();

        return $this->series;
    }

    /**
     * @return mixed
     */
    protected function getPostsQuery()
    {
        $query = Post::whereHas('series', function ($query) {
            $query->whereTranslatable('slug', $this->series->slug);
        });

        $tagIds = $this->series->tags->lists('id');

        if (!empty($tagIds) && $this->includeTaggedPosts) {
            $query->orWhereHas('tags', function ($tag) use ($tagIds) {
                $tag->whereIn('id', $tagIds);
            });
        }

        $query->isPublished();

        return $query;
    }

    protected function setPostUrl(Post $post)
    {
        $post->setUrl(
            $this->postPage,
            $this->controller,
            [
                self::URL_PARAM_NAME => $this->series->slug
            ]
        );
    }
}
