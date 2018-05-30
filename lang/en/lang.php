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
    ]
];
