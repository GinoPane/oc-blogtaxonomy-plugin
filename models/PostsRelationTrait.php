<?php

namespace GinoPane\BlogTaxonomy\Models;

trait PostsRelationTrait
{
    /**
     * Gets a list of items related to Posts for frontend use
     *
     * @param       $query
     * @param array $options Available options are "sort", "displayEmpty", "limit"
     */
    public function scopeListFrontend($query, array $options = [])
    {

    }
}