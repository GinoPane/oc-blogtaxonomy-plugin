<?php

namespace GinoPane\BlogTaxonomy\Models;

use October\Rain\Database\Builder;

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
    public function scopeListFrontend(Builder $query, array $options = [])
    {
        $this->withRelation($query);

        $this->queryOrderBy($query, $options);

        $this->queryDisplayEmpty($query, $options);

        $this->queryPostSlug($query, $options);

        $this->queryLimit($query, $options);

        return $query->get();
    }

    /**
     * @param Builder $query
     * @param array   $options
     *
     * @return void
     */
    private function queryDisplayEmpty(Builder $query, array $options)
    {
        if (empty($options['displayEmpty'])) {
            $query->withCount(
                [
                    'posts' => function ($query) {
                        $query->isPublished();
                    }
                ]
            )->where('posts_count', '>', 0);
        }
    }

    /**
     * @param Builder $query
     * @param array   $options
     *
     * @return void
     */
    private function queryPostSlug(Builder $query, array $options)
    {
        if (!empty($options['post'])) {
            $query->whereHas(
                'posts',
                function ($query) use ($options) {
                    $query->whereSlug($options['post']);
                }
            );
        }
    }

    /**
     * @param Builder $query
     * @param array   $options
     *
     * @return void
     */
    private function queryLimit(Builder $query, array $options)
    {
        if (!empty($options['limit'])) {
            $query->take($options['limit']);
        }
    }

    /**
     * @param Builder $query
     * @param array   $options
     *
     * @return void
     */
    private function queryOrderBy(Builder $query, array $options)
    {
        if (in_array($options['sort'], array_keys(self::$sortingOptions))) {
            if ($options['sort'] == 'random') {
                $query->inRandomOrder();
            } else {
                list($sortField, $sortDirection) = explode(' ', $options['sort']);

                if ($sortField == 'posts_count') {
                    $query->withCount(
                        [
                            'posts' => function ($query) {
                                $query->isPublished();
                            }
                        ]
                    );
                }

                $query->orderBy($sortField, $sortDirection);
            }
        }
    }

    /**
     * @param Builder $query
     *
     * @return void
     */
    private function withRelation(Builder $query)
    {
        $query->with(
            [
                'posts' => function ($query) {
                    $query->isPublished();
                }
            ]
        );
    }
}