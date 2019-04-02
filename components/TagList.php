<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TagList
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class TagList extends ComponentAbstract
{
    const NAME = 'tagList';

    use TranslateArrayTrait;

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
     * Filter tags for the post defined by slug
     *
     * @var string
     */
    public $postSlug;

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
            'displayEmpty' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],
            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_validation_message',
                'showExternalParam' => false
            ],
            'orderBy' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.order_description',
                'type'              => 'dropdown',
                'default'           => 'name asc',
                'showExternalParam' => false
            ],
            'postSlug' => [
                'title'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.post_slug_title',
                'description' => Plugin::LOCALIZATION_KEY . 'components.tag_list.post_slug_description',
                'type'        => 'string'
            ],
            'tagsPage' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_title',
                'group'         =>  Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'description'   => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_description',
                'type'          => 'dropdown',
                'default'       => 'blog/tag',
                'showExternalParam' => false
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
        $order = $this->translate(Tag::$sortingOptions);

        asort($order);

        return $order;
    }

    /**
     * Prepare and return a tag list
     *
     * @return void
     */
    public function onRun()
    {
        $this->tagsPage = $this->getProperty('tagsPage');
        $this->orderBy = $this->getProperty('orderBy');
        $this->postSlug = $this->property('postSlug');
        $this->displayEmpty = $this->getProperty('displayEmpty');
        $this->limit =  $this->getProperty('limit');

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
            'limit' => $this->limit,
            'post' => $this->postSlug
        ]);

        $tagComponent = $this->getComponent(TagPosts::NAME, $this->tagsPage);

        $this->setUrls($tags, $this->tagsPage, $this->controller, ['tag' => $this->urlProperty($tagComponent, 'tag')]);

        return $tags;
    }
}
