<?php

namespace GinoPane\BlogTaxonomy;

use Event;
use Backend;
use System\Classes\PluginBase;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Models\Tag;
use GinoPane\BlogTaxonomy\Models\Series;
use Backend\Behaviors\RelationController;
use RainLab\Blog\Models\Post as PostModel;
use GinoPane\BlogTaxonomy\Components\TagList;
use GinoPane\BlogTaxonomy\Components\TagPosts;
use GinoPane\BlogTaxonomy\Components\SeriesList;
use GinoPane\BlogTaxonomy\Components\SeriesPosts;
use GinoPane\BlogTaxonomy\Components\RelatedPosts;
use RainLab\Blog\Controllers\Posts as PostsController;
use GinoPane\BlogTaxonomy\Components\SeriesNavigation;
use RainLab\Blog\Controllers\Categories as CategoriesController;

/**
 * Class Plugin
 *
 * @package GinoPane\BlogTaxonomy
 */
class Plugin extends PluginBase
{
    const LOCALIZATION_KEY = 'ginopane.blogtaxonomy::lang.';

    const DIRECTORY_KEY = 'ginopane/blogtaxonomy';

    const REQUIRED_PLUGIN_RAINLAB_BLOG = 'RainLab.Blog';

    /**
     * @var array   Require the RainLab.Blog plugin
     */
    public $require = [self::REQUIRED_PLUGIN_RAINLAB_BLOG];

    /**
     * Returns information about this plugin
     *
     * @return  array
     */
    public function pluginDetails()
    {
        return [
            'name'        => self::LOCALIZATION_KEY . 'plugin.name',
            'description' => self::LOCALIZATION_KEY . 'plugin.description',
            'author'      => 'Gino Pane',
            'icon'        => 'icon-tags',
            'homepage'    => 'https://github.com/ginopane/oc-blog-taxonomy'
        ];
    }

    /**
     * Register components
     *
     * @return  array
     */
    public function registerComponents()
    {
        return [
            TagList::class          => TagList::NAME,
            TagPosts::class         => TagPosts::NAME,
            RelatedPosts::class     => RelatedPosts::NAME,
            SeriesList::class       => SeriesList::NAME,
            SeriesPosts::class      => SeriesPosts::NAME,
            SeriesNavigation::class => SeriesNavigation::NAME,
        ];
    }

    /**
     * Boot method, called right before the request route
     */
    public function boot()
    {
        // extend the post model
        $this->extendModel();

        // extend posts functionality
        $this->extendPostsController();

        // extend categories functionality
        $this->extendCategoriesController();
    }

    /**
     * Register plugin navigation
     */
    public function registerNavigation()
    {
        // Extend the navigation
        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems(self::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', [
                'series' => [
                    'label' => self::LOCALIZATION_KEY . 'navigation.series',
                    'icon' => 'icon-list-alt',
                    'code' => 'series',
                    'owner' => self::REQUIRED_PLUGIN_RAINLAB_BLOG,
                    'url' => Backend::url(self::DIRECTORY_KEY . '/series')
                ],

                'tags' => [
                    'label' => self::LOCALIZATION_KEY . 'navigation.tags',
                    'icon'  => 'icon-tags',
                    'code'  => 'tags',
                    'owner' => self::REQUIRED_PLUGIN_RAINLAB_BLOG,
                    'url'   => Backend::url(self::DIRECTORY_KEY . '/tags')
                ]
            ]);
        });
    }

    /**
     * Extend RainLab Post model
     */
    private function extendModel()
    {
        PostModel::extend(function ($model) {
            $model->belongsToMany['tags'] = [
                Tag::class,
                'table' => Tag::CROSS_REFERENCE_TABLE_NAME,
                'order' => 'name'
            ];

            $model->belongsTo['series'] = [
                Series::class,
                'key' => Series::TABLE_NAME . "_id"
            ];
        });
    }

    /**
     * Extends post controller functionality
     */
    private function extendPostsController()
    {
        PostsController::extendFormFields(function ($form, $model) {
            if (!$model instanceof PostModel) {
                return;
            }

            $tab = self::LOCALIZATION_KEY . 'navigation.taxonomy';

            $categoriesConfig = $form->getField('categories')->config;
            $categoriesConfig['tab'] = $tab;
            $categoriesConfig['mode'] = 'relation';
            $categoriesConfig['type'] = 'taglist';
            $categoriesConfig['label'] = 'rainlab.blog::lang.post.tab_categories';
            $categoriesConfig['comment'] = "rainlab.blog::lang.post.categories_comment";
            $categoriesConfig['placeholder'] = self::LOCALIZATION_KEY . 'placeholders.categories';
            unset($categoriesConfig['commentAbove']);

            $form->removeField('categories');

            $form->addSecondaryTabFields([
                'categories' => $categoriesConfig,
                'tags' => [
                    'label' => self::LOCALIZATION_KEY . 'form.tags.label',
                    'comment' => self::LOCALIZATION_KEY . 'form.tags.comment',
                    'mode' => 'relation',
                    'tab' => $tab,
                    'type' => 'taglist',

                    /**
                     * Placeholders are not supported yet by the core.
                     * PR is waiting: https://github.com/octobercms/october/pull/3453
                     */
                    'placeholder' => self::LOCALIZATION_KEY . 'placeholders.tags',
                ],
                'series' => [
                    'label' => self::LOCALIZATION_KEY . 'form.series.label',
                    'tab' => $tab,
                    'type' => 'relation',
                    'nameFrom' => 'title',
                    'comment' => self::LOCALIZATION_KEY . 'form.series.comment',
                    'emptyOption' => self::LOCALIZATION_KEY . 'placeholders.series'
                ],
            ]);
        });
    }

    /**
     * Extends categories controller functionality
     */
    private function extendCategoriesController()
    {
        CategoriesController::extend(function (Controller $controller) {
            $controller->implement[] = RelationController::class;
            $relationConfig = '$/' . self::DIRECTORY_KEY . '/controllers/category/config_relation.yaml';

            if (property_exists($controller, 'relationConfig')) {
                $controller->relationConfig = $controller->mergeConfig(
                    $controller->relationConfig,
                    $relationConfig
                );
            } else {
                $controller->addDynamicProperty('relationConfig', $relationConfig);
            }

            $formConfig = '$/' . self::DIRECTORY_KEY . '/controllers/category/config_form.yaml';

            if (property_exists($controller, 'formConfig')) {
                $controller->formConfig = $controller->mergeConfig(
                    $controller->formConfig,
                    $formConfig
                );
            } else {
                $controller->addDynamicProperty('formConfig', $formConfig);
            }
        });
    }
}
