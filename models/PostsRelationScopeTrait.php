<?php

namespace GinoPane\BlogTaxonomy\Models;

trait PostsRelationScopeTrait
{
    /**
     * @var array
     *
     * public static $sortingOptions
     */

    /**
     * Gets a list of items related to Posts for frontend use
     *
     * @param       $query
     * @param array $options Available options are "sort", "displayEmpty", "limit"
     *
     * @return mixed
     */
    public function scopeListFrontend($query, array $options = [])
    {
        if (in_array($options['sort'], array_keys(self::$sortingOptions))) {
            if ($options['sort'] == 'random') {
                $query->inRandomOrder();
            } else {
                list($sortField, $sortDirection) = explode(' ', $options['sort']);

                if ($sortField == 'posts_count') {
                    $query->withCount('posts');
                }

                $query->orderBy($sortField, $sortDirection);
            }
        }

        if (empty($options['displayEmpty'])) {
            $query->has('posts');
        }

        // Limit the number of results
        if (!empty($options['limit'])) {
            $query->take($options['limit']);
        }

        return $query->with([
                'posts' => function($query){
                    $query->isPublished();
                }
            ]
        )->get();
    }
}