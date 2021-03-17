<?php

namespace GinoPane\BlogTaxonomy\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Plugin;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Behaviors\RelationController;
use GinoPane\BlogTaxonomy\Models\PostType;

/**
 * Class PostTypes
 *
 * @package GinoPane\BlogTaxonomy\Controllers
 */
class PostTypes extends Controller
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

        BackendMenu::setContext(Plugin::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', 'post_types');
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
        $postType = PostType::whereId($recordId)->first();

        if ($postType !== null) {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.post_types.edit_title', ['post_type' => $postType->name]);
        } else {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.post_types.post_type_does_not_exist');
        }

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    /**
     * Remove multiple post types
     *
     * @return mixed
     */
    public function onBulkDelete()
    {
        if ($checkedIds = (array)post('checked', [])) {
            $delete = PostType::whereIn('id', $checkedIds)->delete();
        }

        if (empty($delete)) {
            Flash::error(e(trans(Plugin::LOCALIZATION_KEY . 'form.errors.unknown')));

            return;
        }

        Flash::success(e(trans(Plugin::LOCALIZATION_KEY . 'form.post_types.delete_post_types_success')));

        return $this->listRefresh();
    }
}
