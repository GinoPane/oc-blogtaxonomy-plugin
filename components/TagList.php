<?php

namespace GinoPane\BlogTaxonomy\Components;

use DB;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TagList
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class TagList extends ComponentBase
{
    /**
     * @var Collection | array
     */
    public $tags = [];

    /**
     * Reference to the page name for linking to series
     *
     * @var string
     */
    public $tagsPage;

    /**
     * Component Registration
     *
     * @return  array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.tag_list.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.tag_list.description'
        ];
    }

    /**
     * Component Properties
     *
     * @return  array
     */
    public function defineProperties()
    {
        return [
            'tagsPage' => [
                'title'       => 'Series Page',
                'description' => 'The page where the single series are displayed.',
                'type'        => 'dropdown',
                'default'     => 'blog/series'
            ],
            'hideOrphans' => [
                'title'             => 'Hide orphaned tags',
                'description'       => 'Hides tags with no associated posts.',
                'showExternalParam' => false,
                'type'              => 'checkbox',
            ],
            'results' => [
                'title'             => 'Results',
                'description'       => 'Number of tags to display (zero displays all tags).',
                'type'              => 'string',
                'default'           => '5',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The results must be a number.',
                'showExternalParam' => false
            ],
            'orderBy' => [
                'title'             => 'Sort by',
                'description'       => 'The value used to sort tags.',
                'type'              => 'dropdown',
                'options' => [
                    false           => 'Posts',
                    'name'          => 'Name',
                    'created_at'    => 'Created at'
                ],
                'default'           => false,
                'showExternalParam' => false
            ],
            'direction' => [
                'title'             => 'Order',
                'description'       => 'The order to sort tags in.',
                'type'              => 'dropdown',
                'options' => [
                    'asc'           => 'Ascending',
                    'desc'          => 'Descending',
                ],
                'default'           => 'desc',
                'showExternalParam' => false
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getTagsPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Query and return blog posts
     *
     * @return Collection
     */
    public function onRun()
    {
        $this->tagsPage = $this->property('tagsPage');

        // Start building the tags query
        $query = Tag::with('posts');

        // Hide orphans
        if ($this->property('hideOrphans')) {
            $query->has('posts', '>', 0);
        }

        // Sort the tags
        $subQuery = DB::raw('(
            select count(*)
            from ginopane_blogtaxonomy_post_tag
            where ginopane_blogtaxonomy_post_tag.tag_id = ginopane_blogtaxonomy_tags.id
        )');

        $key = $this->property('orderBy') ?: $subQuery;
        $query->orderBy($key, $this->property('direction'));

        // Limit the number of results
        if ($take = intval($this->property('results')))
            $query->take($take);

        $tags = $query->get();

        foreach ($tags as $key => $item) {
            $item->setUrl($this->tagsPage, $this->controller);
        }

        $this->tags = $tags;
    }
}
