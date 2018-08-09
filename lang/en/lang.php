<?php

return [

    // plugin
    'plugin' => [
        'name' => 'Blog Taxonomy Extension',
        'description' => 'Adds tags and series management to RainLab Blog posts, which are put along with categories in a brand new taxonomy tab',
    ],

    // form
    'form' => [
        'fields' => [
            'tag' => 'Tag',
            'title' => 'Title',
            'slug' => 'Slug',
            'description' => 'Description',
            'posts' => 'Posts'
        ],
        'tags' => [
            'label' => 'Tags',

            'create_form_title' => 'Create a New Tag',
            'edit_form_title' => 'Edit the Tag',

            'delete_confirm' => 'Do you really want to delete this tag?',

            'comment' => 'Select tags the blog post belongs to',

            'name_invalid' => 'Tag names may only contain alpha-numeric characters, spaces and hyphens',
            'name_required' => 'The tag field is required',
            'name_unique' => 'That tag name is already taken',
            'name_too_short' => 'Tag name minimal length is :min'
        ],
        'series' => [
            'label' => 'Series',
            'comment' => 'Choose a series the blog post belongs to',

            'title_invalid' => 'Series names may only contain alpha-numeric characters, spaces and hyphens',
            'title_required' => 'The series title field is required',
            'title_unique' => 'That series title is already taken',
            'title_too_short' => 'Series title minimal length is :min'
        ]
    ],

    'list' => [
        'columns' => [
            'title' => 'Title',
            'posts' => 'Posts',
            'tag' => 'Tag',
            'slug' => 'Slug'
        ]
    ],

    // navigation
    'navigation' => [
        'tags' => 'Tags',
        'series' => 'Series',
        'taxonomy' => 'Taxonomy'
    ],

    // placeholders
    'placeholders' => [
        'tags' => 'Add tags...',
        'series' => 'Choose a series...',
        'categories' => 'Add categories...',
        'title' => 'Enter title...',
        'slug' => 'Enter slug...',
        'no_posts_available' => 'No posts available'
    ],

    // component-specific strings
    'components' => [
        'partials' => [
            'published_in' => 'published in',
            'displaying_number_of_posts' => 'Displaying %d of %d posts',
            'viewing_page_of' => 'Viewing page %d of %d',
            'pagination_back' => 'Back',
            'pagination_next' => 'Next'
        ],
        'series_posts' => [
            'name' => 'Posts in the Series',
            'description' => 'Lists all posts in the supplied series',
            'series_title' => 'Series slug',
            'series_description' => 'Look up the series using the supplied slug value from this URL parameter',
            'posts_in_series' => 'Posts included into the series',
            'no_posts_message' => 'No posts in the series'
        ],
        'tag_list' => [
            'name' => 'Tag List',
            'description' => 'Displays a list of tags',

            'display_empty_title' => 'Display empty tags',
            'display_empty_description' => 'Show tags which don\'t have any posts assigned',

            'order_title' => 'Order by',
            'order_description' => 'How tags should be ordered',

            'limit_title' => 'Limit',
            'limit_description' => 'Number of tags to display, 0 retrieves all tags',

            'tags_page_title' => 'Tag page',
            'tags_page_description' => 'The page where the single tag content is displayed',

            'post_slug_title' => 'Post slug',
            'post_slug_description' => 'Get tags for the post specified by slug value from URL parameter',

            'no_tags_message' => 'No tags found',

            'limit_validation_message' => 'Limit of tags must be a valid non-negative integer number'
        ],
        'tag_posts' => [
            'name' => 'Posts With the Tag',
            'description' => 'Lists all posts with the supplied tag',
            'no_posts_message' => 'No posts with this tag',
            'posts_with_tag' => 'Posts with the tag',
            'tag_title' => 'Tag slug',
            'tag_description' => 'Look up the tag using the supplied slug value from this URL parameter'
        ],
        'series_navigation' => [
            'name' => 'Post Series Navigation',
            'description' => 'Provides navigation within the series for a single post',

            'series_page_title' => 'Series Page',
            'series_page_description' => 'The page where the single series content is displayed',

            'post_slug_title' => 'Post slug',
            'post_slug_description' => 'Get series navigation for the post specified by slug value from URL parameter',

            'links_group' => 'Links',
            'part_of_a_series' => 'This post is part of a series called'
        ],
        'series_list' => [
            'name' => 'Series List',
            'description' => 'Displays a list of series',

            'series_page_title' => 'Series page',
            'series_page_description' => 'The page where the single series content is displayed',

            'series_slug_title' => 'Series slug parameter',
            'series_slug_description' => 'The setting must be equal to slug parameter being used for Series Page (e.g. /blog/series/:series will give you :series)',

            'limit_title' => 'Limit',
            'limit_description' => 'The number of series to display, 0 retrieves all series',

            'display_empty_title' => 'Display Empty Series',
            'display_empty_description' => 'Show series which don\'t have any posts assigned',

            'order_title' => 'Order by',
            'order_description' => 'How series should be ordered',

            'no_series_message' => 'No series found',

            'limit_validation_message' => 'Limit of series must be a valid non-negative integer number'
        ],
        'related_posts' => [
            'name' => 'Related Posts',
            'description' => 'Provides a list of posts related by tags',

            'post_slug_title' => 'Post slug',
            'post_slug_description' => 'Get related posts for the post specified by slug value from URL parameter',

            'no_posts_message' => 'No related posts found',
            'related_posts' => 'Related posts',
            'links_group' => 'Links'
        ],
        'post_list_abstract' => [
            'pagination_group' => 'Pagination',
            'page_parameter_title' => 'Page parameter',
            'page_parameter_description' => 'Calculate pagination based on this URL parameter',
            'pagination_per_page_title' => 'Items per page',
            'pagination_per_page_description' => 'How many items (if any) should be displayed per page',
            'links_group' => 'Links'
        ]
    ],

    // order-by options
    'order_options' => [
        'created_at_asc' => 'Created (ascending)',
        'created_at_desc' => 'Created (descending)',

        'name_asc' => 'Name (ascending)',
        'name_desc' => 'Name (descending)',

        'published_at_asc' => 'Published (ascending)',
        'published_at_desc' => 'Published (descending)',

        'post_count_asc' => 'Post count (ascending)',
        'post_count_desc' => 'Post count (descending)',

        'random' => 'Random',

        'relevance_asc' => 'Relevance (ascending)',
        'relevance_desc' => 'Relevance (descending)',

        'title_asc' => 'Title (ascending)',
        'title_desc' => 'Title (descending)',

        'updated_at_asc' => 'Updated (ascending)',
        'updated_at_desc' => 'Updated (descending)',
    ]
];
