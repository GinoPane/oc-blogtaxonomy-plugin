<?php

namespace GinoPane\BlogTaxonomy\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use GinoPane\BlogTaxonomy\Plugin;
use Illuminate\Support\Facades\DB;
use GinoPane\BlogTaxonomy\Models\Series;
use RainLab\Blog\Models\Post as BlogPost;

/**
 * Class SeriesPosts
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
class SeriesPosts extends ComponentBase
{
    /**
     * @var Series
     */
    public $series;

    /**
     * Message to display when there are no posts
     *
     * @var string
     */
    public $noPostsMessage;

    /**
     * Reference to the page name for linking to posts
     *
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories
     *
     * @var string
     */
    public $categoryPage;

    /**
     * If the post list should be ordered by another attribute
     *
     * @var string
     */
    public $sortOrder;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => Plugin::LOCALIZATION_KEY . 'components.series_posts.name',
            'description' => Plugin::LOCALIZATION_KEY . 'components.series_posts.description'
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'Slug',
                'description' => 'Look up the series using the supplied slug value.',
                'type'        => 'string',
                'default'     => '{{ :slug }}',
            ],
            'noPostsMessage' => [
                'title'        => 'rainlab.blog::lang.settings.posts_no_posts',
                'description'  => 'rainlab.blog::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'rainlab.blog::lang.settings.posts_order',
                'description' => 'rainlab.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at asc'
            ],
            'categoryPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_category',
                'description' => 'rainlab.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
        ];
    }

    /**
     * @see \RainLab\Blog\Components\Posts::getCategoryPageOptions()
     * @return mixed
     */
    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @see \RainLab\Blog\Components\Posts::getPostPageOptions()
     * @return mixed
     */
    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * @see BlogPost::$allowedSortingOptions
     *
     * @return mixed
     */
    public function getSortOrderOptions()
    {
        return BlogPost::$allowedSortingOptions;
    }

    /**
     * @see \RainLab\Blog\Components\Posts::onRun()
     * @return mixed
     */
    public function onRun()
    {
        $this->prepareVars();

        // load posts
        $this->series = $this->page['series'] = $this->listSeries();

        if (is_null($this->series)) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }
    }

    /**
     * Prepare variables
     */
    protected function prepareVars()
    {
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');
        $this->sortOrder = $this->page['sortOrder'] = $this->property('sortOrder');

        // Page links
        $this->postPage = $this->page['postPage' ] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    /**
     * Get Series
     * @return mixed
     */
    protected function listSeries()
    {
        // get serie
        $slug = $this->property('slug');
        $series = Series::with([
            'posts' => function ($query) {
                $query->isPublished();

                $sort = $this->property('sortOrder');
                /*
                 * Sorting
                 * @see \RainLab\Blog\Models\Post::scopeListFrontEnd()
                 */
                if (!is_array($sort)) {
                    $sort = [$sort];
                }

                foreach ($sort as $sorting) {
                    if (in_array($sorting, array_keys(BlogPost::$allowedSortingOptions))) {
                        $parts = explode(' ', $sorting);
                        if (count($parts) < 2) {
                            array_push($parts, 'desc');
                        }
                        list($sortField, $sortDirection) = $parts;
                        if ($sortField == 'random') {
                            $sortField = DB::raw('RAND()');
                        }
                        $query->orderBy($sortField, $sortDirection);
                    }
                }
            },
            'posts.categories'
        ])
            ->where('slug', $slug)
            ->first();

        // Add a "url" helper attribute for linking to each post and category
        if ($series && $series->posts->count()) {
            $series->posts->each([$this, 'setUrls']);
        }

        return $series;
    }

    /**
     * Set Urls to posts
     * @param \RainLab\Blog\Models\Post $post
     */
    public function setUrls(BlogPost $post)
    {
        $post->setUrl($this->postPage, $this->controller);

        if ($post && $post->categories->count()) {
            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }
    }
}
