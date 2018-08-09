# Blog Taxonomy

Taxonomy extension for [RainLab.Blog](https://github.com/rainlab/blog-plugin) plugin.

## Table of Contents
* [Changes to Original Blog Plugin](#changes-to-original-blog-plugin)
* [Implementing Frontend Pages](#implementing-frontend-pages)
    * [Post Series Navigation](#post-series-navigation)
    * [Posts With the Tag](#posts-with-the-tag)
    * [Posts in the Series](#posts-in-the-series)
    * [Related Posts](#related-posts)
    * [Series List](#series-list)
    * [Tag List](#tag-list)

Inspired by [Blog Tags Extension](https://octobercms.com/plugin/bedard-blogtags) and [Blog Series](https://octobercms.com/plugin/pkleindienst-blogseries)
plugins Blog Taxonomy adds both tags and series functionality in a high quality and reliable way. Every post could belong
to a single series and at the same time it can have multiple tags assigned.

> [Categories](https://www.wpbeginner.com/beginners-guide/categories-vs-tags-seo-best-practices-which-one-is-better/) are meant for broad grouping of your posts. Think of these as general topics or the table of contents for your site.
Categories are there to help identify what your blog is really about. It is to assist readers finding the right type of 
content on your site. Categories are hierarchical, so you can define sub-categories.

> Tags are meant to describe specific details of your posts. Think of these as your site’s index words. They are the
micro-data that you can use to micro-categorize your content. Tags are not hierarchical.

And series could help you to organize your posts in a single flow of related posts.

## Changes to Original Blog Plugin

Original blog categories were enhanced with ability to add posts while being on the single category page.
They were also placed along with tags and series in their own **Taxonomy** tab of a CMS blog page.

## Implementing Frontend Pages

The Blog Taxonomy plugin provides several useful components with basic markup for frontend usage. The default markup
is not intended to fit to any existing frontend theme, so if the default markup is not suitable for your website,
feel free to copy it from the default partial and replace the markup with your own.

Available components:

* **Post Series Navigation** (`seriesNavigation`) - provides navigation within the series for a single post.
* **Posts With the Tag** (`postsWithTag`) - lists all posts with the supplied tag.
* **Posts in the Series** (`postsInSeries`) - lists all posts in the supplied series.
* **Related Posts** (`relatedPosts`) - provides a list of posts related by tags.
* **Series List** (`seriesList`) - displays a list of series.
* **Tag List** (`tagList`) - displays a list of tags.

### Post Series Navigation

### Posts With the Tag

### Posts in the Series

### Related Posts

### Series List

### Tag List