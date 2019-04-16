<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Tag;
use October\Rain\Database\Collection;

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
    private $tagsPage;

    /**
     * If the tag list should be ordered by another attribute
     *
     * @var string
     */
    private $orderBy;

    /**
     * Whether display empty tags or not
     *
     * @var bool
     */
    private $displayEmpty;

    /**
     * Filter tags for the post defined by slug
     *
     * @var string
     */
    private $postSlug;

    /**
     * Limits the number of records to display
     *
     * @var int
     */
    public $limit;

    /**
     * Whether count overall amount of tags or count amount of tags under "limit" only
     *
     * @var
     */
    private $exposeTotalCount;

    /**
     * Contains either the total number of tags in the list limited by "limit", or
     * the total number of tags which may be more than the limit if "exposeTotalCount" is used
     *
     * @var int
     */
    public $totalCount;

    /**
     * Component Registration
     *
     * @return  array
     */
    public function componentDetails(): array
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
    public function defineProperties(): array
    {
        return [

            //General properties
            'displayEmpty' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.display_empty_description',
                'type'              => 'checkbox',
                'default'           => false,
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

            //Limit properties
            'limit' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_title',
                'group'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_group',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_description',
                'type'              => 'string',
                'default'           => '0',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_validation_message',
                'showExternalParam' => false
            ],
            'exposeTotalCount' => [
                'title'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.expose_total_count_title',
                'group'             => Plugin::LOCALIZATION_KEY . 'components.tag_list.limit_group',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.tag_list.expose_total_count_description',
                'type'              => 'checkbox',
                'default'           => false,
                'showExternalParam' => false
            ],

            //Links
            'tagsPage' => [
                'title'         => Plugin::LOCALIZATION_KEY . 'components.tag_list.tags_page_title',
                'group'         => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
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
        $this->displayEmpty = (bool) $this->getProperty('displayEmpty');
        $this->limit =  (int) $this->getProperty('limit');
        $this->exposeTotalCount =  (bool) $this->getProperty('exposeTotalCount');

        $this->tags = $this->listTags();
    }

    /**
     * @return Collection
     */
    private function listTags(): Collection
    {
        $tags = Tag::listFrontend([
            'sort' => $this->orderBy,
            'displayEmpty' => $this->displayEmpty,
            'limit' => $this->limit,
            'post' => $this->postSlug
        ]);

        $this->handleCount($tags);
        $this->handleTagUrls($tags);

        return $tags;
    }

    /**
     * @param $tags
     */
    private function handleTagUrls($tags)
    {
        $tagComponent = $this->getComponent(TagPosts::NAME, $this->tagsPage);

        $this->setUrls($tags, $this->tagsPage, $this->controller, ['tag' => $this->urlProperty($tagComponent, 'tag')]);
    }

    /**
     * @param $tags
     */
    private function handleCount($tags)
    {
        $this->totalCount = $tags->count();

        if ($this->exposeTotalCount && ($this->limit > 0) && ($this->totalCount === $this->limit)) {
            $this->totalCount = Tag::listFrontend([
                'displayEmpty' => $this->displayEmpty,
                'post' => $this->postSlug
            ])->count();
        }
    }
}
