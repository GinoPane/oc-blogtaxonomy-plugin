# Blog Taxonomy

Taxonomy extension for [RainLab Blog](https://octobercms.com/plugin/rainlab-blog) plugin.

[![GitHub tag](https://img.shields.io/github/tag/ginopane/oc-blogtaxonomy-plugin.svg)](https://github.com/GinoPane/oc-blogtaxonomy-plugin)
[![Maintainability](https://api.codeclimate.com/v1/badges/60ecdc5d75bb0e490049/maintainability)](https://codeclimate.com/github/GinoPane/oc-blogtaxonomy-plugin/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GinoPane/oc-blogtaxonomy-plugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GinoPane/oc-blogtaxonomy-plugin/?branch=master)

## Table of Contents
* [Changes to Original Blog Plugin](#changes-to-original-blog-plugin)
  * [Categories](#categories) 
  * [Tags](#tags)
  * [Series](#series)
  * [Types](#types)
* [Translate Plugin Support](#translate-plugin-support)
* [Migration from Other Plugins](#migration-from-other-plugins)
* [Implementing Frontend Pages](#implementing-frontend-pages)
  * [Post Series Navigation](#post-series-navigation)
  * [Posts in the Series](#posts-in-the-series)
  * [Posts With the Tag](#posts-with-the-tag)
  * [Related Posts](#related-posts)
  * [Series List](#series-list)
  * [Tag List](#tag-list)
  * [Related Series](#related-series)

Inspired by [Blog Tags Extension](https://octobercms.com/plugin/bedard-blogtags) and [Blog Series](https://octobercms.com/plugin/pkleindienst-blogseries)
plugins Blog Taxonomy adds both tags and series functionality in a high quality and reliable way. Every post could belong
to a single series and at the same time it can have multiple tags assigned.

## Changes to Original Blog Plugin

### Categories

> [Categories](https://www.wpbeginner.com/beginners-guide/categories-vs-tags-seo-best-practices-which-one-is-better/) are meant for broad grouping of your posts. Think of these as general topics or the table of contents for your site.
Categories are there to help identify what your blog is really about. It is to assist readers finding the right type of
content on your site. Categories are hierarchical, so you can define sub-categories.

Original blog categories were enhanced with ability to add posts while being on a single category page.

They were also placed in a new tag-like style along with tags and series in their own **Taxonomy** tab of a backend CMS blog page.

Also, categories can now (since version 3.0.0) have a cover image, and an array of featured images (`cover_image` and
`featured_images` attributes respectively). Two new backend settings were added to enable or disable this extension.

### Tags

> Tags are meant to describe specific details of your posts. Think of these as your site’s index words. They are the
micro-data that you can use to micro-categorize your content. Tags are not hierarchical.

### Series

Series could help you to organize your posts in a single flow of related posts.

### Types

Since 3.0.0 posts could have types assigned. Post types support can be enabled via backend settings for
the plugin. The type is a set of properties that could be associated with the post. Types are being created and edited as separated models in the backend. Each post type defines several
properties which are added as additional form inputs when a specific type is chosen.

## Translate Plugin Support

Starting from 1.5.0 version Blog Taxonomy supports [RainLab Translate](https://octobercms.com/plugin/rainlab-translate) plugin when it's installed. All tag and series fields could be translated.

## Migration from Other Plugins

Starting from 1.12.0 version Blog Taxonomy supports migration from other plugins. Currently supported plugins are: [BlogSeries](https://github.com/PascalKleindienst/october-blogseries-extension).

The migration is done via console command:

```php artisan blogtaxonomy:migrate PKleindienst.BlogSeries```

Use `-h` or `--help` to get usage help.

Migration example output:

```bash
**************************************************
*     Migration from PKleindienst.BlogSeries     *
**************************************************

Migrating series
2 series found
Series "Series 1" => Blog Taxonomy Series "Series 1" (#3)
Series "Series 2" => Blog Taxonomy Series "Series 2" (#4)
All series have been migrated

Migrating related series
Relation "#4" => "#3" added
Relation "#3" => "#4" added
Related series have been migrated


 Do you want to assign newly created series to posts (already assigned Blog Taxonomy series will be overwritten) (yes/no) [no]:
 > yes

Migrating series assigned to posts
Series "#3" has been assigned to a post
Series "#4" has been assigned to a post
Migrated series has been assigned

Migration from PKleindienst.BlogSeries finished
```

## Implementing Frontend Pages

### Post Types

Post types provide additional attributes which could be assigned to blog posts. Each attribute when created got its
own `code` (like a `slug` for a Post, Tag, Series, etc.). This code can be used to access attribute values for
individual posts. For this purpose such methods as were added:

```
typeAttribute(string code) - to get one attribute
typeAttributes() - to get all attributes
``` 

So, for example, if you have a "Rating" attribute with a `rating` code you can show it in the template like this:
```
<span>{{ post.typeAttribute('rating') }}</span>
```

### Components

The Blog Taxonomy plugin provides several useful components with a basic markup for frontend usage. The default markup
is **not intended to fit to any existing frontend** theme, it is just an example, so if the default markup is not suitable for your website,
feel free to copy it from the default partial and replace the markup with your own.

Available components:

* **Post Series Navigation** (`seriesNavigation`) - provides navigation within the series for a single post.
* **Posts in the Series** (`postsInSeries`) - lists all posts in the supplied series.
* **Posts With the Tag** (`postsWithTag`) - lists all posts with the supplied tag.
* **Related Posts** (`relatedPosts`) - provides a list of posts related by tags.
* **Series List** (`seriesList`) - displays a list of series.
* **Tag List** (`tagList`) - displays a list of tags.
* **Related Series** (`relatedSeries`) - provides a list of related series.

#### Post Series Navigation

Component `seriesNavigation` provides navigation within the series for a single post. You can display, for example, next and previous posts in the same series,
show series details and link to its page, etc.

Component properties:

* **Post slug** - get series navigation for the post specified by slug value from URL parameter; e.g. if post slug is `:post`
  the page URL must contain `:post` parameter which value will be used as post slug to retrieve the series;
* **Series page** - CMS page which contains [`postsInSeries`](#posts-in-the-series) component and is used to display a single series content and posts;
* **Post page** - name of the blog post page to display a single blog post content.

#### Posts in the Series

Component `postsInSeries` lists all posts in the supplied series. The component supports pagination and posts ordering.

Available properties:

* **Series slug** - look up the series using the supplied slug value from this URL parameter; e.g. if series slug is `:series`
  the page URL must contain `:series` parameter which value will be used as series slug to retrieve the series;
* **Post order** - attribute and direction on which posts should be ordered;
* **Page parameter** - calculate pagination based on this URL parameter;
* **Items per page** - how many items (if any) should be displayed per page, "0" displays all items;
* **Post page** - name of the blog post page to display a single blog post content;
* **Category page** - name of the category page to display a single blog category content;
* **Include tagged posts** - additionally include posts tagged with the tags from the current series;
* **Include categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts only with
  these categories will be included into the list;
* **Exclude posts** - list of post ids/slugs (can be mixed together) separated by comma; these posts will be excluded
  from the list;
* **Exclude categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts with these
  categories will be excluded from the list.

#### Posts With the Tag

Component `postsWithTag` lists all posts with the supplied tag. The component supports pagination and posts ordering.

Available properties:

* **Tag slug** - look up the tag using the supplied slug value from this URL parameter; e.g. if tag slug is `:tag`
  the page URL must contain `:tag` parameter which value will be used as tag slug to retrieve the tag;
* **Include series posts** - additionally include posts which belongs to the series tagged with the current tag;
* **Post order** - attribute and direction on which posts should be ordered;
* **Page parameter** - calculate pagination based on this URL parameter;
* **Items per page** - how many items (if any) should be displayed per page, "0" displays all items;
* **Post page** - name of the blog post page to display a single blog post content;
* **Category page** - name of the category page to display a single blog category content;
* **Include categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts only with
  these categories will be included into the list;
* **Exclude posts** - list of post ids/slugs (can be mixed together) separated by comma; these posts will be excluded
  from the list;
* **Exclude categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts with these categories will be excluded from the list.

#### Related Posts

Component `relatedPosts` provides a list of posts related by tags, e.g. posts which have some tags in common.

Available properties:

* **Post slug** - get related posts for the post specified by slug value from URL parameter; e.g. if post slug is `:post`
  the page URL must contain `:post` parameter which value will be used as post slug to retrieve the related posts;
* **Limit** - number of posts to display, 0 retrieves all related posts;
* **Post order** - attribute and direction on which posts should be ordered;
* **Post page** - name of the blog post page to display a single blog post content.

#### Series List

Component `seriesList` displays a list of series.

Available properties:

* **Display empty series** - whether to show series which don't have any posts assigned or not;
* **Limit** - number of series to display, 0 retrieves all series;
* **Series order** - how series list should be ordered;
* **Series page** - CMS page which contains [`postsInSeries`](#posts-in-the-series) component and is used to display a single series content and posts;
* **Fetch related posts** - if enabled, the component will fetch related posts, so they are properly (**as published**) available via `posts` property of the series item; it does an additional request, so decreases performance a little;
* **Include categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts only with
  these categories will be included into the post count and post list associated with the series;
* **Exclude posts** - list of post ids/slugs (can be mixed together) separated by comma; these posts will be excluded from the post count and post list associated with the series;
* **Exclude categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts with these categories will be excluded from from the post count and post list associated with the series.

#### Tag List

Component `tagList` displays a list of tags. Can be used to build a tag cloud (because post count with each tag is available).
It also can be used to retrieve a list of tags for specific post.

> Please don't forget to use different aliases for components if you use the same component several times on the same page
(presumably for different purpose)

Available properties:

* **Display empty tags** - whether to show tags which were no assigned to any posts or not;
* **Tag order** - how tags should be ordered;
* **Limit** - number of tags to display, 0 retrieves all tags;
* **Expose total count** - the component has `totalCount` property which would contain either overall amount of tags or
  amount of tags under "limit" only. For example you have 10 tags overall but you use a **limit** of 5. This will make component
  to display 5 tags only. With **Expose total count** enabled you could still get "10" in `totalCount`. And you'll get 5 otherwise;
* **Fetch tagged posts** - if enabled, the component will fetch tagged posts, so they are properly (**as published**) available via `posts` property of the tag item; it does an additional request, so decreases performance a little;
* **Include series tags** - if enabled, the component will additionally include tags applied to the post's series if
  the post has series and the series has tags;
* **Debug output** - allows to enable debug output to the browser's console. Need to be implemented by the theme;
* **Fetch series post count** - if enabled, the component will additionally fetch count of posts which belong to
  series tagged with this tag, so it will be possible to create more accurately weighed tag cloud;
* **Include categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts only with
  these categories will be included into the post count and post list associated with the tag;
* **Exclude posts** - list of post ids/slugs (can be mixed together) separated by comma; these posts will be excluded from the post count and post list associated with the tag;
* **Exclude categories** - list of categories ids/slugs (can be mixed together) separated by comma; posts with these categories will be excluded from the post count and post list associated with the tag.

> Leave this as `false` if you do not require a whole total count, because it will give you more optimised result

* **Include tag filter** - whether to include a tag filter input or not. Tag filter is a text input with some JavaScript
  powered by [mark.js](https://markjs.io/) that allows you to filter a tag list nicely. Use it if you have a lot of tags to
  display, because it would allow to search for a specific tag quickly. Possible values are "Never", "Always" and
  "When tag total count > limit". All of them are self-descriptive, but keep in mind that the last option should be used
  when both **Limit** and **Expose total count** enabled, because you'd get probably an undesired result otherwise.
  The component exposes `tagFilterEnabled` property which would be set to `true` when the filter assets are included and
  the filter could be used;

> The default markup injects JavaScript into `{% scripts %}` placeholder so please make sure your theme has it. Also, the
default code requires jQuery. If you do not have it, please make sure to adjust the code to fulfill your needs

* **Post slug** - get tags for the post specified by a slug value from URL parameter; e.g. if the post slug is `:post`
  the page URL must contain `:post` parameter which value will be used as post slug to retrieve tags;

> It is not required to set it to a real value unless you want to display tags specific for the post

* **Tag page** - CMS page which contains [`postsWithTag`](#posts-with-the-tag) component and is used to display a single tag content (its posts);
* **Tags page** - CMS page which probably contains [`tagList`](#tag-list) component and is used to display all tags you have;

#### Related Series

Component `relatedSeries` provides a list of related series. The same list could be fetched for individual series by accessing its property `related_series`. The only difference is that the component fills urls for related series.

Available properties:

* **Series slug** - look up the related series using the supplied slug value from this URL parameter; e.g. if series slug is `:series`
  the page URL must contain `:series` parameter which value will be used as series slug to retrieve the related series;
* **Series page** - CMS page which contains [`postsInSeries`](#posts-in-the-series) component and is used to display a single series content and posts.
