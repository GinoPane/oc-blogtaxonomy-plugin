<?php

namespace GinoPane\BlogTaxonomy\Models;

use Str;
use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * Class Tag
 *
 * @property string name
 * @property string slug
 *
 * @package GinoPane\BlogTaxonomy\Models
 */
class Tag extends ModelAbstract
{
    use Sluggable;
    use Validation;

    const TABLE_NAME = 'ginopane_blogtaxonomy_tags';

    const CROSS_REFERENCE_TABLE_NAME = 'ginopane_blogtaxonomy_post_tag';

    /**
     * @var string The database table used by the model
     */
    public $table = self::TABLE_NAME;

    /**
     * Specifying of implemented behaviours as strings is convenient when
     * the target behaviour could be missing due to disabled or not installed
     * plugin. You won't get an error, the plugin would simply work without model
     *
     * @var array
     */
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * Translatable properties, indexed property will be available in queries
     *
     * @var array
     */
    public $translatable = [
        'name',
        [
            'slug',
            'index' => true
        ]
    ];

    /**
     * @var array
     */
    protected $slugs = ['slug' => 'name'];

    /**
     * Relations
     *
     * @var array
     */
    public $belongsToMany = [
        'posts' => [
            Post::class,
            'table' => self::CROSS_REFERENCE_TABLE_NAME,
            'order' => 'published_at desc'
        ]
    ];

    /**
     * Fillable fields
     *
     * @var array
     */
    public $fillable = [
        'name',
        'slug',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'name' => "required|unique:" . self::TABLE_NAME . "|min:2|regex:/^[\w\-\. ]+$/iu",
        'slug' => "required|unique:" . self::TABLE_NAME . "|min:2|regex:/^[\w\-]+$/iu"
    ];

    /**
     * The tag might be created via tag list and therefore it won't have a slug
     */
    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug) {
            $this->slug = Str::slug($this->name);
        }
    }

    /**
     * Validation messages
     *
     * @var array
     */
    public $customMessages = [
        'name.required' => Plugin::LOCALIZATION_KEY . 'form.tags.name_required',
        'name.unique'   => Plugin::LOCALIZATION_KEY . 'form.tags.name_unique',
        'name.regex'    => Plugin::LOCALIZATION_KEY . 'form.tags.name_invalid',
        'name.min'      => Plugin::LOCALIZATION_KEY . 'form.tags.name_too_short',

        'slug.required' => Plugin::LOCALIZATION_KEY . 'form.tags.slug_required',
        'slug.unique'   => Plugin::LOCALIZATION_KEY . 'form.tags.slug_unique',
        'slug.regex'    => Plugin::LOCALIZATION_KEY . 'form.tags.slug_invalid',
        'slug.min'      => Plugin::LOCALIZATION_KEY . 'form.tags.slug_too_short',
    ];

    /**
     * The attributes on which the post list can be ordered
     *
     * @var array
     */
     public static $sortingOptions = [
        'name asc' => Plugin::LOCALIZATION_KEY . 'order_options.name_asc',
        'name desc' => Plugin::LOCALIZATION_KEY . 'order_options.name_desc',
        'created_at asc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_asc',
        'created_at desc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_desc',
        'posts_count asc' => Plugin::LOCALIZATION_KEY . 'order_options.post_count_asc',
        'posts_count desc' => Plugin::LOCALIZATION_KEY . 'order_options.post_count_desc',
        'random' => Plugin::LOCALIZATION_KEY . 'order_options.random'
    ];

    /**
     * @param array $params
     *
     * @return array
     */
    protected function getModelUrlParams(array $params): array
    {
        return [
            array_get($params, 'tag', 'tag') => $this->slug
        ];
    }
}
