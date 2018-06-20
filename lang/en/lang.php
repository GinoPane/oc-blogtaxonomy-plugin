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

            'name_invalid' => 'Tag names may only contain alpha-numeric characters, spaces and hyphens',
            'name_required' => 'The tag field is required',
            'name_unique' => 'That tag name is already taken',
            'name_too_short' => 'Tag name minimal length is :min'
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
        'series_posts' => [
            'name' => 'Posts in Series',
            'description' => 'Lists all posts in the series'
        ],
        'tag_list' => [
            'name' => 'Tag List',
            'description' => 'Displays a list of tags'
        ],
        'tag_posts' => [
            'name' => 'Posts With the Tag',
            'description' => 'Lists all posts with the tag'
        ],
        'series_navigation' => [
            'name' => 'Post Series Navigation',
            'description' => 'Provides navigation within the series for a single post'
        ],
        'series_list' => [
            'name' => 'Series List',
            'description' => 'Displays a list of series',

            'series_page_title' => 'Series Page',
            'series_page_description' => 'The page where the single series content is displayed',

            'display_empty_title' => 'Display Empty Series',
            'display_empty_description' => 'Show series which don\'t have any posts assigned',

            'order_title' => 'Order By',
            'order_description' => 'How series should be ordered',
        ],
        'related_posts' => [
            'name' => 'Related Posts',
            'description' => 'Provides a list of posts related by tags'
        ]
    ],

    'order_options' => [
        'title_asc' => 'Title (ascending)',
        'title_desc' => 'Title (descending)',
        'created_at_asc' => 'Created (ascending)',
        'created_at_desc' => 'Created (descending)',
        'updated_at_asc' => 'Updated (ascending)',
        'updated_at_desc' => 'Updated (descending)',
        'published_at_asc' => 'Published (ascending)',
        'published_at_desc' => 'Published (descending)',
        'relevance_asc' => 'Title (ascending)',
        'relevance_desc' => 'Title (descending)',
        'random' => 'Random'
    ]
];
