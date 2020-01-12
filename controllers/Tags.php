<?php

namespace GinoPane\BlogTaxonomy\Controllers;

use Backend\Behaviors\RelationController;
use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Models\Tag;
use GinoPane\BlogTaxonomy\Plugin;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;

/**
 * Class Tags
 *
 * @package GinoPane\BlogTaxonomy\Controllers
 */
class Tags extends Controller
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

    /**
     * Controller configs
     *
     * @var string
     */
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * Tags constructor
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext(Plugin::REQUIRED_PLUGIN_RAINLAB_BLOG, 'blog', 'tags');
    }

    /**
     * Remove multiple tags
     *
     * @return mixed
     */
    public function onBulkDelete()
    {
        if ($checkedIds = (array)post('checked', [])) {
            $delete = Tag::whereIn('id', $checkedIds)->delete();
        }

        if (empty($delete)) {
            Flash::error(e(trans(Plugin::LOCALIZATION_KEY . 'form.errors.unknown')));

            return;
        }

        Flash::success(e(trans(Plugin::LOCALIZATION_KEY . 'form.tags.delete_tags_success')));

        return $this->listRefresh();
    }

    /**
     * Removes tags with no associated posts
     *
     * @return mixed
     */
    public function index_onRemoveOrphanedTags()
    {
        if (!Tag::has('posts', 0)->has('series', 0)->count() ) {
            Flash::warning(e(trans(Plugin::LOCALIZATION_KEY . 'form.tags.no_orphaned_tags')));

            return;
        }

        if (!Tag::has('posts', 0)->has('series', 0)->delete()) {
            Flash::error(e(trans(Plugin::LOCALIZATION_KEY . 'form.errors.unknown')));

            return;
        }

        Flash::success(e(trans(Plugin::LOCALIZATION_KEY . 'form.tags.remove_orphaned_tags_success')));

        return $this->listRefresh();
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
        $tag = Tag::whereId($recordId)->first();

        if ($tag !== null) {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.tags.edit_title', ['tag' => $tag->name]);
        } else {
            $this->pageTitle = trans(Plugin::LOCALIZATION_KEY . 'form.tags.tag_does_not_exist');
        }

        return $this->asExtension('FormController')->update($recordId, $context);
    }
}
