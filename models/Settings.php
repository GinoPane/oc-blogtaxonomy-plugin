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

    public $implement = [SettingsModel::class];

    public $settingsCode = self::SETTINGS_CODE;

    public $settingsFields = 'fields.yaml';

    public function postTypesEnabled() : bool
    {
        return (bool) $this->{self::POST_TYPES_ENABLED_KEY};
    }
} 