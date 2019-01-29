<?php

namespace GinoPane\BlogTaxonomy\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use GinoPane\BlogTaxonomy\Models\Tag;
use GinoPane\BlogTaxonomy\Plugin;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use October\Rain\Exception\SystemException;
use October\Rain\Flash\FlashBag;

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
        ListController::class
    ];

    /**
     * Controller configs
     *
     * @var string
     */
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

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
        if (!Tag::has('posts', 0)->count()) {
            Flash::warning(e(trans(Plugin::LOCALIZATION_KEY . 'form.tags.no_orphaned_tags')));

            return;
        }

        if (!Tag::has('posts', 0)->delete()) {
            Flash::error(e(trans(Plugin::LOCALIZATION_KEY . 'form.errors.unknown')));

            return;
        }

        Flash::success(e(trans(Plugin::LOCALIZATION_KEY . 'form.tags.remove_orphaned_tags_success')));

        return $this->listRefresh();
    }

    /**
     * @throws \SystemException
     *
     * @return mixed
     */
    public function onCreateForm()
    {
        $this->asExtension('FormController')->create();

        return $this->makePartial('tag_create_modal_form');
    }

    /**
     * @throws \SystemException
     *
     * @return mixed
     */
    public function onUpdateForm()
    {
        $this->asExtension('FormController')->update(post('record_id'));

        $this->vars['recordId'] = post('record_id');

        return $this->makePartial('tag_update_modal_form');
    }

    /**
     * @return mixed
     */
    public function onCreate()
    {
        $this->asExtension('FormController')->create_onSave();

        return $this->listRefresh('tags');
    }

    /**
     * @return mixed
     */
    public function onUpdate()
    {
        $this->asExtension('FormController')->update_onSave(post('record_id'));

        return $this->listRefresh('tags');
    }

    /**
     * @return mixed
     */
    public function onDelete()
    {
        $this->asExtension('FormController')->update_onDelete(post('record_id'));

        return $this->listRefresh('tags');
    }
}
