<?php

namespace GinoPane\BlogTaxonomy\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Models\Series as SeriesModel;
use GinoPane\BlogTaxonomy\Plugin;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;

/**
 * Class Series
 *
 * @package GinoPane\BlogTaxonomy\Controllers
 */
class Series extends Controller
{
    /**
     * Behaviours implemented by the controller
     *
     * @var array
     */
    public $implement = [
        FormController::class,
        ListController::class,
        RelationController::class
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * Series constructor
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext(Plugin::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', 'series');
    }

    /**
     * Controller "update" action used for updating existing model records.
     * This action takes a record identifier (primary key of the model)
     * to locate the record used for sourcing the existing form values.
     *
     * @param int $recordId Record identifier
     * @param string $context Form context
     * @return void
     */
    public function update($recordId = null, $context = null)
    {
        $series = SeriesModel::whereId($recordId)->first();

        if ($series !== null) {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.series.edit_title', ['series' => $series->title]);
        } else {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.series.series_does_not_exist');
        }

        return $this->asExtension('FormController')->update($recordId, $context);
    }
}
