<?php

namespace GinoPane\BlogTaxonomy\Classes;

use GinoPane\BlogTaxonomy\Plugin;
use October\Rain\Database\Builder;

/**
 * Class PostListExceptionsTrait
 *
 * @package GinoPane\BlogTaxonomy\Classes
 */
trait PostListExceptionsTrait
{
    /**
     * Filter out posts based on their slugs or ids
     *
     * @var array
     */
    protected $exceptPosts;

    /**
     * Filter out posts based on their categories slugs or ids
     *
     * @var array
     */
    protected $exceptCategories;

    /**
     * Properties for list exceptions handling
     *
     * @return array
     */
    private function getPostExceptionProperties(): array
    {
        return [
            'exceptPosts' => [
                'group'             => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.exceptions_group',
                'title'             => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.except_posts_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.except_posts_description',
                'type'              => 'string',
                'default'           => '',
                'showExternalParam' => false,
            ],
            'exceptCategories' => [
                'group'             => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.exceptions_group',
                'title'             => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.except_categories_title',
                'description'       => Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.except_categories_description',
                'type'              => 'string',
                'default'           => '',
                'showExternalParam' => false,
            ],
        ];
    }

    /**
     * @return void
     */
    private function populateExceptions()
    {
        $this->exceptPosts = $this->extractArrayFromProperty('exceptPosts');
        $this->exceptCategories = $this->extractArrayFromProperty('exceptCategories');
    }

    /**
     * @param $query
     */
    private function handlePostExceptions(Builder $query)
    {
        self::handleExceptionsByPost($query, $this->exceptPosts);
        self::handleExceptionsByCategory($query, $this->exceptCategories);
    }

    /**
     * @param string $property
     *
     * @return array
     */
    private function extractArrayFromProperty(string $property): array
    {
        return array_map('trim', array_filter(explode(',', $this->property($property))));
    }

    /**
     * Separates parameters into two arrays: ids and slugs
     *
     * @param array $parameters
     *
     * @return array Ids array an slugs array
     */
    private static function separateParameters(array $parameters): array
    {
        $slugs = $parameters;
        $ids = [];

        foreach ($slugs as $index => $potentialId) {
            if (is_numeric($potentialId)) {
                $ids[] = $potentialId;
                unset($slugs[$index]);
            }
        }

        return [$ids, $slugs];
    }

    /**
     * @param Builder $query
     * @param array   $exceptCategories
     */
    public static function handleExceptionsByCategory(Builder $query, array $exceptCategories)
    {
        if (!empty($exceptCategories)) {
            list($ids, $slugs) = self::separateParameters($exceptCategories);

            $query->whereDoesntHave('categories', static function (Builder $innerQuery) use ($ids, $slugs) {
                if (!empty($ids)) {
                    $innerQuery->whereIn('id', $ids);
                }

                if (!empty($slugs)) {
                    $innerQuery->whereIn('slug', $slugs);
                }
            });
        }
    }

    /**
     * @param Builder $query
     * @param array   $exceptPosts
     */
    public static function handleExceptionsByPost(Builder $query, array $exceptPosts)
    {
        if (!empty($exceptPosts)) {
            list($ids, $slugs) = self::separateParameters($exceptPosts);

            if (!empty($ids)) {
                $query->whereNotIn('id', $ids);
            }

            if (!empty($slugs)) {
                $query->whereNotIn('slug', $slugs);
            }
        }
    }
}