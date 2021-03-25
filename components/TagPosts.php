<?php

namespace GinoPane\BlogTaxonomy\Components;

use Rainlab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;
use GinoPane\BlogTaxonomy\Classes\PostListAbstract;

/**
 * Class TagPosts
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class TagPosts extends PostListAbstract
{
    const NAME = 'postsWithTag';

    /**
     * @var Tag
     */
    public $tag;

    /**
     * @var bool
     */
    protected $includeSeriesPosts = false;

    /**
     * Component Registration
     * @return array
     */
    public function componentDetails(): array
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.tag_posts.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.tag_posts.description'
        ];
    }

    /**
     * Component Properties
     * @return array
     */
    public function defineProperties(): array
    {
        return [
            'tag' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_posts.tag_title',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_posts.tag_description',
                'default'       => '{{ :tag }}',
                'type'          => 'string'
            ],
            'includeSeriesPosts' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_posts.include_series_posts_title',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_posts.include_series_posts_description',
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

        $this->includeSeriesPosts = $this->property('includeSeriesPosts', false);
    }

    /**
     * @inheritDoc
     */
    protected function prepareContextItem()
    {
        // load tag
        $this->tag = $this->page['tag'] = Tag::whereTranslatable('slug', $this->property('tag'))->first();

        return $this->tag;
    }

    /**
     * @return mixed
     */
    protected function getPostsQuery()
    {
        $query = Post::whereHas('tags', function ($query) {
            $query->whereTranslatable('slug', $this->tag->slug);
        });

        if ($this->includeSeriesPosts) {
            $query->orWhereHas('series', function ($query) {
                $query->whereHas('tags', function ($query) {
                    $query->whereTranslatable('slug', $this->tag->slug);
                });
            });
        }

        $query->isPublished();

        return $query;
    }
}
