<?php

namespace GinoPane\BlogTaxonomy\Components;

use RainLab\Blog\Models\Post;

/**
 * Trait UrlHelperTrait
 *
 * @package GinoPane\BlogTaxonomy\Components
 */
trait UrlHelperTrait
{
    /**
     * Reference to the page name for linking to posts
     *
     * @var string
     */
    protected $postPage;

    /**
     * Reference to the page name for linking to categories
     *
     * @var string
     */
    protected $categoryPage;

    /**
     * @param $items
     * @param $urlPage
     * @param $controller
     */
    public function setUrls($items, $urlPage, $controller)
    {
        if ($items) {
            foreach ($items as $item) {
                $item->setUrl($urlPage, $controller);
            }
        }
    }

    /**
     * Set Urls to posts
     *
     * @param Post $post
     */
    public function setPostUrls(Post $post)
    {
        $post->setUrl($this->postPage, $this->controller);

        if ($post && $post->categories->count()) {
            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }
    }
}