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
     * Reference to the page name for linking to tags page
     *
     * @var string
     */
    public $tagsPage;

    /**
     * If the tag list should be ordered by another attribute
     *
     * @var string
     */
    public $sortOrder;

    /**
     * Whether display or not empty tags
     *
     * @var bool
     */
    public $displayEmpty;

    /**
     * Whether limit or not the list length
     *
     * @var int
     */
    public $limit;

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
                'title'       => 'Tags Page',
                'description' => 'The page where the single series are displayed.',
                'type'        => 'dropdown',
                'default'     => 'blog/tag'
            ],
            'hideOrphans' => [
                'title'             => 'Hide orphaned tags',
                'description'       => 'Hides tags with no associated posts.',
                'showExternalParam' => false,
                'type'              => 'checkbox',
            ],
            'limit' => [
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
     * Prepare and return a tag list
     *
     * @return Collection
     */
    public function onRun()
    {
        $this->tagsPage = $this->property('tagsPage', '');
        $this->sortOrder = $this->property('sortOrder', 'title asc');
        $this->displayEmpty = $this->property('displayEmpty', false);
        $this->limit =  $this->property('limit', 0);

        $this->tags = $this->listTags();
    }

    protected function listTags()
    {
        $query = Tag::with('posts');

        if (!$this->displayEmpty) {
            $query->has('posts');
        }

        $query->withCount('posts')->orderBy('posts_count', 'asc');

        // Limit the number of results
        if ($this->limit) {
            $query->take($this->limit);
        }

        $tags = $query->get();

        if ($tags) {
            foreach ($tags as $item) {
                $item->setUrl($this->tagsPage, $this->controller);
            }
        }

        return $tags;
    }
}
