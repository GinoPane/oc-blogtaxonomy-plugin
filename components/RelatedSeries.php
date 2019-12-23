<?php

namespace GinoPane\BlogTaxonomy\Components;

use GinoPane\BlogTaxonomy\Plugin;
use GinoPane\BlogTaxonomy\Models\Series;
use GinoPane\BlogTaxonomy\Classes\TranslateArrayTrait;

/**
 * Class RelatedSeries
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class RelatedSeries extends SeriesList
{
    const NAME = 'relatedSeries';

    use TranslateArrayTrait;

    /**
     * @return array
     */
    public function componentDetails(): array
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.related_series.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.related_series.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties(): array
    {
        return [
            'series' => [
                'title'       => Plugin::LOCALIZATION_KEY . 'components.series_posts.series_title',
                'description' => Plugin::LOCALIZATION_KEY . 'components.series_posts.series_description',
                'type'        => 'string',
                'default'     => '{{ :series }}',
            ],
            'seriesPage' => [
                'group'         =>  Plugin::LOCALIZATION_KEY . 'components.post_list_abstract.links_group',
                'title'         =>  Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_title',
                'description'   =>  Plugin::LOCALIZATION_KEY . 'components.series_list.series_page_description',
                'type'          =>  'dropdown',
                'default'       =>  'blog/series',
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * Prepare and return a series list
     *
     * @return mixed
     */
    public function onRun()
    {
        $this->seriesPage = $this->getProperty('seriesPage');

        $this->series = $this->listRelatedSeries();
    }

    /**
     * Get Series
     *
     * @return mixed
     */
    protected function listRelatedSeries()
    {
        $series = Series::whereTranslatable('slug', $this->property('series'))
            ->with(
                [
                    'related_series' => static function ($query) {
                        $query->withCount(
                            [
                                'posts' => static function ($query) {
                                    $query->isPublished();
                                }
                            ]
                        );
                    }
                ]
            )
            ->first();

        $relatedSeries = $series->related_series;

        $this->handleUrls($relatedSeries);

        return $relatedSeries;
    }
}
