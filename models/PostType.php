<?php

namespace GinoPane\BlogTaxonomy\Models;

use Model;
use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * Class PostType
 *
 * @property string name
 * @property string slug
 * @property string description
 * @property array type_attributes
 *
 * @package GinoPane\BlogTaxonomy\Models
 *
 * @method static PostType find(string $postTypeId)
 */
class PostType extends Model
{
    use Sluggable;
    use Validation;

    public const TABLE_NAME = 'ginopane_blogtaxonomy_post_types';

    /**
     * The database table used by the model
     *
     * @var string
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
     * @var array Attributes to be stored as JSON
     */
    protected $jsonable = ['type_attributes'];

    /**
     * Translatable properties, indexed property will be available in queries
     *
     * @var array
     */
    public $translatable = [
        'name',
        'description'
    ];

    /**
     * Has-many relations
     *
     * @var array
     */
    public $hasMany = [
        'posts' => [
            Post::class,
            'key' => self::TABLE_NAME . "_id"
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $rules = [
        'name' => 'required|unique:' . self::TABLE_NAME . '|min:3',
        'slug'  => 'required|unique:' . self::TABLE_NAME . '|min:3|regex:/^[\w\-]+$/i',
        'type_attributes.*.name' => 'required|unique_in_repeater',
        'type_attributes.*.type' => 'required',
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    public $customMessages = [
        'name.required' => Plugin::LOCALIZATION_KEY . 'form.post_types.name_required',
        'name.unique'   => Plugin::LOCALIZATION_KEY . 'form.post_types.name_unique',
        'name.min'      => Plugin::LOCALIZATION_KEY . 'form.post_types.name_too_short',

        'slug.required' => Plugin::LOCALIZATION_KEY . 'form.post_types.slug_required',
        'slug.unique'   => Plugin::LOCALIZATION_KEY . 'form.post_types.slug_unique',
        'slug.regex'    => Plugin::LOCALIZATION_KEY . 'form.post_types.slug_invalid',
        'slug.min'      => Plugin::LOCALIZATION_KEY . 'form.post_types.slug_too_short',

        'type_attributes.*.name.required' => Plugin::LOCALIZATION_KEY . 'form.post_types.type_attributes_name_required',
        'type_attributes.*.type.required' => Plugin::LOCALIZATION_KEY . 'form.post_types.type_attributes_type_required',
        'type_attributes.*.name.unique_in_repeater' => Plugin::LOCALIZATION_KEY . 'form.post_types.type_attributes_name_unique',
    ];

    protected $slugs = ['slug' => 'name'];
}
