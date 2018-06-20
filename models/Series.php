<?php

namespace GinoPane\BlogTaxonomy\Models;

use Model;
use Cms\Classes\Controller;
use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;
use Illuminate\Support\Facades\DB;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;

/**
 * Class Series
 *
 * @property string slug
 *
 * @package GinoPane\BlogTaxonomy\Models
 */
class Series extends Model
{
    use Validation;
    use Sluggable;
    use PostsRelationScopeTrait;

    const TABLE_NAME = 'ginopane_blogtaxonomy_series';



    /**
     * The database table used by the model
     *
     * @var string
     */
    public $table = self::TABLE_NAME;

    /**
     * Relations
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
        'title' => "required|unique:" . self::TABLE_NAME . "|min:3|regex:/^[a-z0-9\- ]+$/i",
        'slug'  => "required|unique:" . self::TABLE_NAME . "|min:3|regex:/^[a-z0-9\-]+$/i"
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    public $customMessages = [
        'title.required' => Plugin::LOCALIZATION_KEY . 'lang.form.name_required',
        'title.unique'   => Plugin::LOCALIZATION_KEY . 'lang.form.name_unique',
        'title.regex'    => Plugin::LOCALIZATION_KEY . 'lang.form.name_invalid',
        'title.min'      => Plugin::LOCALIZATION_KEY . 'lang.form.name_too_short',
    ];

    /**
     * The attributes on which the post list can be ordered
     *
     * @var array
     */
    public static $sortingOptions = [
        'title asc' => Plugin::LOCALIZATION_KEY . 'order_options.title_asc',
        'title desc' => Plugin::LOCALIZATION_KEY . 'order_options.title_desc',
        'created_at asc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_asc',
        'created_at desc' => Plugin::LOCALIZATION_KEY . 'order_options.created_at_desc',
        'posts_count asc' => Plugin::LOCALIZATION_KEY . 'order_options.post_count_asc',
        'posts_count desc' => Plugin::LOCALIZATION_KEY . 'order_options.post_count_desc',
        'random' => Plugin::LOCALIZATION_KEY . 'order_options.random'
    ];

    /**
     * @var array
     */
    protected $slugs = ['slug' => 'title'];

    /**
     * @return mixed
     */
    public function getPostCountAttribute()
    {
        return $this->posts()->isPublished()->count();
    }

    /**
     * Sets the URL attribute with a URL to this object
     *
     * @param string                $pageName
     * @param Controller            $controller
     *
     * @return void
     */
    public function setUrl($pageName, $controller): void
    {
        $params = [
            'slug' => $this->slug,
        ];

        $this->url = $controller->pageUrl($pageName, $params);
    }
}
