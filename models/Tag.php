<?php

namespace GinoPane\BlogTaxonomy\Models;

use Model;
use Cms\Classes\Controller;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use RainLab\Blog\Models\Post;
use GinoPane\BlogTaxonomy\Plugin;

/**
 * Class Tag
 *
 * @package GinoPane\BlogTaxonomy\Models
 */
class Tag extends Model
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
        'name' => "required|unique:" . self::TABLE_NAME . "|min:3|regex:/^[a-z0-9\- ]+$/i",
        'slug' => "required|unique:" . self::TABLE_NAME . "|min:3|regex:/^[a-z0-9\-]+$/i"
    ];

    /**
     * Validation messages
     *
     * @var array
     */
    public $customMessages = [
        'name.required' => Plugin::LOCALIZATION_KEY . 'lang.form.name_required',
        'name.unique'   => Plugin::LOCALIZATION_KEY . 'lang.form.name_unique',
        'name.regex'    => Plugin::LOCALIZATION_KEY . 'lang.form.name_invalid',
        'name.min'      => Plugin::LOCALIZATION_KEY . 'lang.form.name_too_short',
    ];

    /**
     * Sets the URL attribute with a URL to this object
     *
     * @param string                $pageName
     * @param Controller            $controller
     *
     * @return string
     */
    public function setUrl($pageName, $controller): string
    {
        $params = [
            'tag' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }
}
