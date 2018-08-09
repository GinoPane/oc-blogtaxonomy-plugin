<?php

namespace GinoPane\BlogTaxonomy\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
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
}
