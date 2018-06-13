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
    use UrlHelperTrait;

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
    public $orderBy;

    /**
     * Whether display or not empty tags
     *
     * @var bool
     */
    public $displayEmpty;

    /**
     * Limits the number of records to display
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
                'title'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_title',
                'description' => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_description',
                'type'        => 'dropdown',
                'default'     => 'blog/tag'
            ],
            'displayEmpty' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_description',
                'type'              => 'checkbox',
                'default'           => false
            ],
            'orderBy' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_description',
                'type'              => 'dropdown',
                'default'           => 'name asc',
            ],
            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.tag_list.validation_message'
            ],
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
     * @return mixed
     */
    public function getOrderByOptions()
    {
        return Tag::$sortingOptions;
    }

    /**
     * Prepare and return a tag list
     *
     * @return void
     */
    public function onRun()
    {
        $this->tagsPage = $this->property('tagsPage', '');
        $this->orderBy = $this->property('orderBy', 'name asc');
        $this->displayEmpty = $this->property('displayEmpty', false);
        $this->limit =  $this->property('limit', 0);

        $this->tags = $this->listTags();
    }

    /**
     * @return mixed
     */
    protected function listTags()
    {
        $tags = Tag::listFrontend([
            'sort' => $this->orderBy,
            'displayEmpty' => $this->displayEmpty,
            'limit' => $this->limit
        ]);

        $this->setUrls($tags, $this->tagsPage, $this->controller);

        return $tags;
    }
}
