<?php

namespace GinoPane\BlogTaxonomy\Components;

use Rainlab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;

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
     * Component Registration
     * @return array
     */
    public function componentDetails()
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
    public function defineProperties()
    {
        $properties = [
            'tag' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_posts.tag_title',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_posts.tag_description',
                'default'       => '{{ :tag }}',
                'type'          => 'string'
            ]
        ] + parent::defineProperties();

        return $properties;
    }

    /**
     * Prepare variables
     */
    protected function prepareContextItem()
    {
        // load tag
        $tag = new Tag();

        $tag = $tag->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $tag->transWhere('slug', $this->property('tag'))
            : $tag->where('slug', $this->property('tag'));

        $this->tag = $tag->first();

        return $this->tag;
    }

    /**
     * @return mixed
     */
    protected function getPostsQuery()
    {
        $query = Post::whereHas('tags', function ($query) {
            $query->where('slug', $this->tag->slug);
        })->isPublished();

        return $query;
    }
}
