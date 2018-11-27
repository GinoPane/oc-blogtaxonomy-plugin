<?php

namespace GinoPane\BlogTaxonomy\Models;

use Model;
use Cms\Classes\Controller;

/**
 * Class ModelAbstract
 *
 * @property string $url
 *
 * @package GinoPane\BlogTaxonomy\Models
 */
abstract class ModelAbstract extends Model
{
    /**
     * Sets the URL attribute with a URL to this object
     *
     * @param string $pageName
     * @param Controller $controller
     * @param array $params
     *
     * @return void
     */
    public function setUrl($pageName, Controller $controller, array $params = array())
    {
        $params = $this->getModelUrlParams($params);

        $this->url = $controller->pageUrl($pageName, $params);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    abstract protected function getModelUrlParams(array $params): array;
}
