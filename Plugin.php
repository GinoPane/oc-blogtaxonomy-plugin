<?php

namespace GinoPane\BlogTaxonomy;

use Event;
use Input;
use Backend;
use Exception;
use Validator;
use System\Models\File;
use Backend\Widgets\Form;
use Backend\Widgets\Lists;
use Backend\Widgets\Filter;
use System\Classes\PluginBase;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Models\Tag;
use GinoPane\BlogTaxonomy\Models\Series;
use Backend\Behaviors\RelationController;
use RainLab\Blog\Models\Post as PostModel;
use GinoPane\BlogTaxonomy\Models\Settings;
use GinoPane\BlogTaxonomy\Models\PostType;
use GinoPane\BlogTaxonomy\Components\TagList;
use GinoPane\BlogTaxonomy\Components\TagPosts;
use GinoPane\BlogTaxonomy\Components\SeriesList;
use GinoPane\BlogTaxonomy\Components\SeriesPosts;
use RainLab\Blog\Models\Category as CategoryModel;
use GinoPane\BlogTaxonomy\Components\RelatedPosts;
use GinoPane\BlogTaxonomy\Components\RelatedSeries;
use GinoPane\BlogTaxonomy\Console\MigrateFromPlugin;
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

    const DEFAULT_ICON = 'icon-sitemap';

    /**
     * @var array   Require the RainLab.Blog plugin
     */
    public $require = [
        'RainLab.Blog'
    ];

    /**
     * @var Settings
     */
    private $settings;

    /**
     * Returns information about this plugin
     *
     * @return  array
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => self::LOCALIZATION_KEY . 'plugin.name',
            'description' => self::LOCALIZATION_KEY . 'plugin.description',
            'author'      => 'Siarhei <Gino Pane> Karavai',
            'icon'        => self::DEFAULT_ICON,
            'homepage'    => 'https://github.com/GinoPane/oc-blogtaxonomy-plugin'
        ];
    }

    /**
     * Register components
     *
     * @return  array
     */
    public function registerComponents(): array
    {
        return [
            TagList::class          => TagList::NAME,
            TagPosts::class         => TagPosts::NAME,
            RelatedPosts::class     => RelatedPosts::NAME,
            SeriesList::class       => SeriesList::NAME,
            SeriesPosts::class      => SeriesPosts::NAME,
            SeriesNavigation::class => SeriesNavigation::NAME,
            RelatedSeries::class    => RelatedSeries::NAME
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand(MigrateFromPlugin::NAME, MigrateFromPlugin::class);
    }

    /**
     * Boot method, called right before the request route
     */
    public function boot(): void
    {
        $this->extendValidator();

        $this->extendPostModel();

        $this->extendPostListColumns();

        $this->extendPostFilterScopes();

        $this->extendPostsController();

        $this->extendCategoriesModel();

        $this->extendCategoriesController();

        $this->extendCategoriesFormFields();
    }

    private function getSettings(): Settings
    {
        return $this->settings ?: $this->settings = Settings::instance();
    }

    /**
     * Register plugin navigation
     * - add tags and series menu items
     *
     * @return void
     */
    public function registerNavigation(): void
    {
        // Extend the navigation
        Event::listen('backend.menu.extendItems', function ($manager) {
            $manager->addSideMenuItems(self::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', [
                'series' => [
                    'label' => self::LOCALIZATION_KEY . 'navigation.sidebar.series',
                    'icon' => 'icon-list-alt',
                    'code' => 'series',
                    'owner' => self::REQUIRED_PLUGIN_RAINLAB_BLOG,
                    'url' => Backend::url(self::DIRECTORY_KEY . '/series')
                ],

                'tags' => [
                    'label' => self::LOCALIZATION_KEY . 'navigation.sidebar.tags',
                    'icon'  => 'icon-tags',
                    'code'  => 'tags',
                    'owner' => self::REQUIRED_PLUGIN_RAINLAB_BLOG,
                    'url'   => Backend::url(self::DIRECTORY_KEY . '/tags')
                ]
            ]);

            if ($this->getSettings()->postTypesEnabled()) {
                $manager->addSideMenuItems(self::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', [
                    'post_types' => [
                        'label' => self::LOCALIZATION_KEY . 'navigation.sidebar.post_types',
                        'icon'  => 'icon-cog',
                        'code'  => 'post_types',
                        'owner' => self::REQUIRED_PLUGIN_RAINLAB_BLOG,
                        'url'   => Backend::url(self::DIRECTORY_KEY . '/posttypes')
                    ]
                ]);
            }
        });
    }

    /**
     * Register plugin settings
     *
     * @return array
     */
    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label'       => self::LOCALIZATION_KEY . 'plugin.name',
                'description' => self::LOCALIZATION_KEY . 'plugin.description',
                'icon'        => self::DEFAULT_ICON,
                'class'       => Settings::class,
                'order'       => 100
            ]
        ];
    }

    /**
     * Extend RainLab Post model
     * - add tags relation
     * - add series relation
     * - add post type relation
     *
     * @return void
     */
    private function extendPostModel(): void
    {
        PostModel::extend(function ($model) {
            $model->morphToMany = [
                'tags' => [Tag::class, 'name' => Tag::PIVOT_COLUMN]
            ];

            $model->belongsTo['series'] = [
                Series::class,
                'key' => Series::TABLE_NAME . "_id"
            ];

            if ($this->getSettings()->postTypesEnabled()) {
                $model->belongsTo['post_type'] = [
                    PostType::class,
                    'key' => PostType::TABLE_NAME . "_id"
                ];

                $model->addJsonable(PostType::TABLE_NAME. '_attributes');

                $model->addDynamicMethod('typeAttributes', function () use ($model) {
                    if (!empty($model->post_type->id)) {
                        $rawFields = $model->{PostType::TABLE_NAME. '_attributes'}[0] ?? [];
                        $prefix = $model->post_type->id.'.';
                        $fields = [];

                        foreach ($rawFields as $code => $value) {
                            if (strpos($code, $prefix) === 0) {
                                $fields[str_replace($prefix, '', $code)] = $value;
                            }
                        }

                        return $fields;
                    }

                    return [];
                });

                $model->addDynamicMethod('typeAttribute', function (string $code) use ($model) {
                    if (!empty($model->post_type->id)) {
                        $attributeKey = sprintf('%s.%s', $model->post_type->id, $code);

                        return $model->{PostType::TABLE_NAME. '_attributes'}[0][$attributeKey] ?? null;
                    }

                    return $model->post_type->id;
                });

                $model->addDynamicMethod('scopeFilterPostTypes', function ($query, array $types) {
                    return $query->whereHas('post_type', function ($query) use ($types) {
                        $query->whereIn('id', $types);
                    });
                });
            }
        });
    }

    /**
     * Extends post controller functionality
     * - transform categories into taglist and move then into taxonomy tab
     * - add tags and series properties
     *
     * @throws Exception
     *
     * @return void
     */
    private function extendPostsController(): void
    {
        PostsController::extendFormFields(function (Form $form, $model) {
            if (!$model instanceof PostModel) {
                return;
            }

            /*
             * When extending the form, you should check to see if $formWidget->isNested === false
             * as the Repeater FormWidget includes nested Form widgets which can cause your changes
             * to be made in unexpected places.
             *
             * @link https://octobercms.com/docs/plugin/extending#extending-backend-form
             */
            if (!empty($form->isNested)) {
                return;
            }

            $tab = self::LOCALIZATION_KEY . 'navigation.tab.taxonomy';

            $categoriesConfig = $this->transformPostCategoriesIntoTaglist($form, $tab);

            $form->addSecondaryTabFields([
                'categories' => $categoriesConfig,
                'tags' => [
                    'label' => self::LOCALIZATION_KEY . 'form.tags.label',
                    'comment' => self::LOCALIZATION_KEY . 'form.tags.comment_post',
                    'mode' => 'relation',
                    'tab' => $tab,
                    'type' => 'taglist',
                    'placeholder' => self::LOCALIZATION_KEY . 'placeholders.tags',
                ],
                'series' => [
                    'label' => self::LOCALIZATION_KEY . 'form.series.label',
                    'tab' => $tab,
                    'type' => 'relation',
                    'nameFrom' => 'title',
                    'comment' => self::LOCALIZATION_KEY . 'form.series.comment',
                    'placeholder' => self::LOCALIZATION_KEY . 'placeholders.series'
                ],
            ]);

            if ($this->getSettings()->postTypesEnabled()) {
                $this->addPostTypeAttributes($form, $model);
            }
        });
    }

    /**
     * Extends categories controller functionality
     */
    private function extendCategoriesController(): void
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

    private function extendCategoriesModel(): void
    {
        CategoryModel::extend(function ($model) {
            if ($this->getSettings()->postCategoriesCoverImageEnabled()) {
                $model->attachOne['cover_image'] = [
                    File::class, 'delete' => true
                ];
            }

            if ($this->getSettings()->postCategoriesFeaturedImagesEnabled()) {
                $model->attachMany['featured_images'] = [
                    File::class, 'order' => 'sort_order', 'delete' => true
                ];
            }
        });
    }

    private function extendCategoriesFormFields(): void
    {
        CategoriesController::extendFormFields(function ($form, $model) {
            if (!$model instanceof CategoryModel) {
                return;
            }

            if ($this->getSettings()->postCategoriesCoverImageEnabled() ||
                $this->getSettings()->postCategoriesFeaturedImagesEnabled()
            ) {
                $form->addFields([
                    'images_section' => [
                        'label' => self::LOCALIZATION_KEY . 'form.categories.images_section',
                        'type' => 'section',
                        'comment' => self::LOCALIZATION_KEY . 'form.categories.images_section_comment'
                    ]
                ]);
            }

            if ($this->getSettings()->postCategoriesCoverImageEnabled()) {
                $form->addFields([
                    'cover_image' => [
                        'label'     => self::LOCALIZATION_KEY . 'form.fields.cover_image',
                        'type'      => 'fileupload',
                        'mode'      => 'image',
                        'tab'       => 'Images',
                        'span'      => 'left'
                    ]
                ]);
            }

            if ($this->getSettings()->postCategoriesFeaturedImagesEnabled()) {
                $form->addFields([
                    'featured_images' => [
                        'label'     => self::LOCALIZATION_KEY . 'form.fields.featured_images',
                        'type'      => 'fileupload',
                        'mode'      => 'image',
                        'tab'       => 'Images'
                    ]
                ]);
            }
        });
    }

    private function extendValidator(): void
    {
        if ($this->getSettings()->postTypesEnabled()) {
            Validator::extend('unique_in_repeater', function ($attribute, $value, $parameters, $validator) {
                $attributeNameParts = explode('.', $attribute);

                $repeaterName = reset($attributeNameParts);
                $fieldName = end($attributeNameParts);

                $repeaterData = isset($validator->getData()[$repeaterName])
                    ? (array) $validator->getData()[$repeaterName]
                    : [];

                $fieldData = array_column($repeaterData, $fieldName);

                if (count(array_unique($fieldData)) !== count($fieldData)) {
                    return false;
                }

                return true;
            });
        }
    }

    private function transformPostCategoriesIntoTaglist(Form $form, string $tab)
    {
        $categoriesConfig = $form->getField('categories')->config;
        $categoriesConfig['tab'] = $tab;
        $categoriesConfig['mode'] = 'relation';
        $categoriesConfig['type'] = 'taglist';
        $categoriesConfig['label'] = 'rainlab.blog::lang.post.tab_categories';
        $categoriesConfig['comment'] = "rainlab.blog::lang.post.categories_comment";
        $categoriesConfig['placeholder'] = self::LOCALIZATION_KEY . 'placeholders.categories';
        unset($categoriesConfig['commentAbove']);

        $form->removeField('categories');
        return $categoriesConfig;
    }

    private function addPostTypeAttributes(Form $form, PostModel $model): void
    {
        $tab = self::LOCALIZATION_KEY . 'navigation.tab.type';

        $form->addSecondaryTabFields([
            'post_type' => [
                'label' => self::LOCALIZATION_KEY . 'form.post_types.label',
                'tab' => $tab,
                'type' => 'relation',
                'nameFrom' => 'name',
                'comment' => self::LOCALIZATION_KEY . 'form.post_types.comment',
                'placeholder' => self::LOCALIZATION_KEY . 'placeholders.post_types'
            ],
        ]);

        $condition = implode(
            array_map(
                static function ($value) {
                    return "[$value]";
                },
                PostType::all()->pluck('id')->toArray()
            )
        );

        $typeAttributes = [
            'label' => self::LOCALIZATION_KEY . 'form.post_types.type_attributes',
            'commentAbove' => self::LOCALIZATION_KEY . 'form.post_types.type_attributes_comment',
            'type' => 'repeater',
            'minItems' => 1,
            // there's October bug related to maxItems option when you can add more than one record
            // though only one is expected. The backend won't allow to save this anyway
            // https://github.com/octobercms/october/issues/5533
            'maxItems' => 1,
            'dependsOn' => 'post_type',
            'trigger' => [
                'action' => 'show',
                'field' => 'post_type',
                'condition' => "value$condition"
            ],
            'sortable' => false,
            'style' => 'accordion',
            'tab' => $tab,
            'form' => [
                'fields' => []
            ]
        ];

        if ((($postTypeId = Input::get('Post.post_type')) !== null &&
                $postType = PostType::find($postTypeId))
            ||
            (!empty($model->id) && !empty($model->post_type->id) && $postType = $model->post_type)
        ) {
            if (!empty($postType->type_attributes)) {
                $fields = [];

                foreach ($postType->type_attributes as $typeAttribute) {
                    if (empty($typeAttribute['code'])) {
                        continue;
                    }

                    $field = [];

                    $type = $typeAttribute['type'] ?? 'text';

                    switch ($type) {
                        case 'file':
                        case 'image':
                            $field['type'] = 'mediafinder';
                            $field['mode'] = $type;
                            $field['imageWidth'] = 200;
                            break;
                        case 'dropdown':
                            $field['type'] = $type;

                            $options = array_map(static function ($value) {
                                return trim($value);
                            }, explode(',', $typeAttribute['dropdown_options'] ?? ''));

                            $field['options'] = $options;

                            break;
                        case 'text':
                        case 'textarea':
                            $field['type'] = $type;
                            break;
                        case 'datepicker':
                            $field['type'] = $type;
                            $field['mode'] = $typeAttribute['datepicker_mode'] ?? 'date';

                            break;
                    }

                    $field['label'] = $typeAttribute['name'] ?? '';

                    $fields[sprintf("%s.%s", $postType->id, $typeAttribute['code'])] = $field;
                }

                $typeAttributes['form']['fields'] = $fields;
            }
        }

        $form->addSecondaryTabFields([
            PostType::TABLE_NAME . '_attributes' => $typeAttributes
        ]);
    }

    private function extendPostListColumns(): void
    {
        Event::listen('backend.list.extendColumns', function (Lists $listWidget) {
            // Only for the Posts controller
            if (!$listWidget->getController() instanceof PostsController) {
                return;
            }

            // Only for the Post model
            if (!$listWidget->model instanceof PostModel) {
                return;
            }

            if ($this->getSettings()->postTypesEnabled()) {
                $listWidget->addColumns([
                    'type' => [
                        'label' => self::LOCALIZATION_KEY . 'form.post_types.post_list_column',
                        'relation' => 'post_type',
                        'select' => 'name',
                        'searchable' => 'true',
                        'sortable' => true
                    ]
                ]);
            }
        });
    }

    private function extendPostFilterScopes(): void
    {
        Event::listen('backend.filter.extendScopes', function (Filter $filterWidget) {
            if ($this->getSettings()->postTypesEnabled()) {
                $filterWidget->addScopes([
                    'type' => [
                        'label' => self::LOCALIZATION_KEY . 'form.post_types.post_list_filter_scope',
                        'modelClass' => PostType::class,
                        'nameFrom' => 'name',
                        'scope' => 'filterPostTypes'
                    ]
                ]);
            }
        });
    }
}
