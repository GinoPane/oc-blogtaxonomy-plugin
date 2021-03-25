<?php

namespace GinoPane\BlogTaxonomy\Models;

use Model;
use System\Behaviors\SettingsModel;

/**
 * Class Settings
 *
 * @package GinoPane\BlogTaxonomy\Models
 */
class Settings extends Model
{
    const SETTINGS_CODE = 'ginopane_blogtaxonomy';

    const POST_TYPES_ENABLED_KEY = 'post_types_enabled';
    const POST_CATEGORIES_COVER_IMAGES_ENABLED_KEY = 'post_categories_cover_image_enabled';
    const POST_CATEGORIES_FEATURED_IMAGES_ENABLED_KEY = 'post_categories_featured_images_enabled';

    public $implement = [SettingsModel::class];

    public $settingsCode = self::SETTINGS_CODE;

    public $settingsFields = 'fields.yaml';

    public function postTypesEnabled() : bool
    {
        return (bool) $this->{self::POST_TYPES_ENABLED_KEY};
    }

    public function postCategoriesCoverImageEnabled() : bool
    {
        return (bool) $this->{self::POST_CATEGORIES_COVER_IMAGES_ENABLED_KEY};
    }

    public function postCategoriesFeaturedImagesEnabled() : bool
    {
        return (bool) $this->{self::POST_CATEGORIES_FEATURED_IMAGES_ENABLED_KEY};
    }
}
