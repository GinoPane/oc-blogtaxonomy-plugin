<?php

return [

    // plugin
    'plugin' => [
        'name' => 'Blog Taxonomy',
        'description' => 'Adds tags and series management for RainLab Blog posts',
    ],

    // form
    'form' => [
        'errors' => [
            'unknown' => 'An unknown error has occurred'
        ],
        'fields' => [
            'tag' => 'Tag',
            'title' => 'Title',
            'images' => 'Images',
            'slug' => 'Slug',
            'description' => 'Description',
            'posts' => 'Posts',
            'series' => 'Series',
            'status' => 'Status',
            'related_series' => 'Related Series'
        ],
        'tabs' => [
            'general' => 'General',
            'posts' => 'Posts',
            'series' => 'Series',
            'related_series' => 'Related Series'
        ],

        'categories' => [
            'no_posts_in_categories' => 'There are no posts in this category'
        ],
        'tags' => [
            'label' => 'Tags',

            'create_title' => 'Create a New Tag',
            'edit_title' => 'Edit the Tag ":tag"',
            'list_title' => 'Manage Tags',
            'new_tag_label' => 'New Tag',
            'no_tags_message' => 'There are no tags. Create some to get started',
            'tag_does_not_exist' => 'The tag does not exist',
            'no_posts_with_such_tag' => 'There are no posts with this tag',
            'no_series_with_such_tag' => 'There are no series with this tag',

            'delete_confirm' => 'Do you really want to delete this tag?',
            'remove_orphaned_load_indicator' => 'Removing orphaned tags...',
            'remove_orphaned_label' => 'Remove Orphaned Tags',
            'remove_orphaned_confirm' => 'Are you sure you want to remove orphaned tags?',
            'no_orphaned_tags' => 'There are no orphaned tags',
            'remove_orphaned_tags_success' => 'Successfully removed orphaned tags',
            'delete_bulk_confirm' => 'Are you sure you want to delete selected tags?',
            'delete_tags_success' => 'Successfully deleted tags',

            'comment_post' => 'Select tags that are applicable to the post',
            'comment_series' => 'Select tags that are applicable to the series',

            'name_invalid' => 'Tag names may only contain alpha-numeric characters, spaces, hyphens and some punctuation',
            'name_required' => 'The tag name field is required',
            'name_unique' => 'This tag name is already taken',
            'name_too_short' => 'Tag name minimal length is :min',

            'slug_invalid' => 'Tag slugs may only contain alpha-numeric characters, spaces and hyphens',
            'slug_required' => 'The tag slug field is required',
            'slug_unique' => 'This tag slug is already taken',
            'slug_too_short' => 'Tag slug minimal length is :min',

            'return_to_list' => 'Return to the tags list',
            'create_load_indicator' => 'Creating a tag...',
            'update_load_indicator' => 'Updating the tag...',
            'delete_load_indicator' => 'Deleting the tag...',
            'new_series_button_label' => 'New Tag',
            'create_button_label' => 'Create',
            'save_button_label' => 'Save',
            'create_and_close_button_label' => 'Create and Close',
            'save_and_close_button_label' => 'Save and Close',
            'cancel_button_label' => 'Cancel',
            'or' => 'or',
        ],
        'series' => [
            'label' => 'Series',
            'description' => 'Description',
            'status' => 'Status',

            'create_title' => 'Create Series',
            'edit_title' => 'Edit the Series ":series"',
            'list_title' => 'Manage Series',
            'series_does_not_exist' => 'The series does not exist',
            'no_series_message' => 'There are no series. Create some to get started',
            'no_posts_in_series' => 'There are no posts in this series',
            'comment' => 'Choose a series the blog post belongs to',

            'title_invalid' => 'Series titles may only contain alpha-numeric characters, spaces, hyphens and some punctuation',
            'title_required' => 'The series title field is required',
            'title_unique' => 'This series title is already taken',
            'title_too_short' => 'Series title minimal length is :min',

            'slug_invalid' => 'Series slugs may only contain alpha-numeric characters, spaces and hyphens',
            'slug_required' => 'The series slug field is required',
            'slug_unique' => 'This series slug is already taken',
            'slug_too_short' => 'Series slug minimal length is :min',

            'create_load_indicator' => 'Creating series...',
            'update_load_indicator' => 'Updating series...',
            'delete_load_indicator' => 'Deleting series...',
            'delete_confirm' => 'Do you really want to delete this series?',
            'delete_bulk_confirm' => 'Are you sure you want to delete selected series?',
            'delete_series_success' => 'Successfully deleted series',
            'new_series_button_label' => 'New Series',
            'create_button_label' => 'Create',
            'save_button_label' => 'Save',
            'create_and_close_button_label' => 'Create and Close',
            'save_and_close_button_label' => 'Save and Close',
            'cancel_button_label' => 'Cancel',
            'or' => 'or',
            'return_to_list' => 'Return to the series list'
        ]
    ],

    'list' => [
        'columns' => [
            'title' => 'Title',
            'posts' => 'Posts',
            'series' => 'Series',
            'tag' => 'Tag',
            'tags' => 'Tags',
            'status' => 'Status',
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
        'name' => 'Enter name...',
        'slug' => 'Enter slug...',
        'no_posts_available' => 'No posts available',
        'no_series_available' => 'No series available'
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
            'display_empty_description' => 'Show tags which were no assigned to any posts',

            'order_title' => 'Tag order',
            'order_description' => 'How tags should be ordered',

            'tag_page_title' => 'Tag page',
            'tag_page_description' => 'The page where a single tag content is displayed',

            'tags_page_title' => 'Tags page',
            'tags_page_description' => 'The page where all tags are listed',

            'post_slug_title' => 'Post slug',
            'post_slug_description' => 'Get tags for the post specified by slug value from URL parameter',
            'fetch_posts_title' => 'Fetch tagged posts',
            'fetch_posts_description' => 'Fetches tagged posts so they are available as `posts` property of the tag item. Slows down performance',
            'include_series_tags_title' => 'Include series tags',
            'include_series_tags_description' => 'Additionally includes tags applied to the post\'s series if the post has series and the series has tags',
            'fetch_series_post_count_title' => 'Fetch series post count',
            'fetch_series_post_count_description' => 'Additionally fetches count of posts which belong to series tagged with this tag',

            'no_tags_message' => 'No tags found',
            'all_tags_link' => 'Show all',

            'limit_validation_message' => 'Limit of tags must be a valid non-negative integer number',

            'limit_group' => 'Limit',

            'limit_title' => 'Limit',
            'limit_description' => 'Number of tags to display, 0 retrieves all tags',
            'expose_total_count_title' => 'Expose total count',
            'expose_total_count_description' => 'Count whether overall amount of tags or amount of tags under "limit" only',
            'tag_filter_title' => 'Include tag filter',
            'tag_filter_description' => 'Whether include tag filter input or not',

            'tag_filter_options' => [
                'never' => 'Never',
                'always' => 'Always',
                'on_overflow' => 'When tag total count > limit'
            ],

            'special_group' => 'Special',
            'debug_output_title' => 'Debug output',
            'debug_output_description' => 'Allows to enable debug output to the browser\'s console. Depends on the theme'
        ],
        'tag_posts' => [
            'name' => 'Posts With the Tag',
            'description' => 'Lists all posts with the supplied tag',
            'no_posts_message' => 'No posts with this tag',
            'posts_with_tag' => 'Posts with the tag',
            'tag_title' => 'Tag slug',
            'tag_description' => 'Look up the tag using the supplied slug value from this URL parameter',
            'include_series_posts_title' => 'Include series posts',
            'include_series_posts_description' => 'Additionally include posts which belongs to the series tagged with the current tag'
        ],
        'series_navigation' => [
            'name' => 'Post Series Navigation',
            'description' => 'Provides navigation within the series for a single post',

            'series_page_title' => 'Series page',
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

            'fetch_posts_title' => 'Fetch related posts',
            'fetch_posts_description' => 'Fetches related posts so they are available as `posts` property of the series item. Slows down performance',

            'limit_title' => 'Limit',
            'limit_description' => 'Number of series to display, 0 retrieves all series',

            'display_empty_title' => 'Display empty series',
            'display_empty_description' => 'Show series which don\'t have any posts assigned',

            'order_title' => 'Series order',
            'order_description' => 'How series list should be ordered',

            'no_series_message' => 'No series found',

            'limit_validation_message' => 'Limit of series must be a valid non-negative integer number'
        ],
        'related_posts' => [
            'name' => 'Related Posts',
            'description' => 'Provides a list of posts related by tags',

            'post_slug_title' => 'Post slug',
            'post_slug_description' => 'Get related posts for the post specified by slug value from URL parameter',

            'limit_title' => 'Limit',
            'limit_description' => 'Number of posts to display, 0 retrieves all related posts',
            'limit_validation_message' => 'Limit of related posts must be a valid non-negative integer number',

            'no_posts_message' => 'No related posts found',
            'related_posts' => 'Related posts',
            'links_group' => 'Links'
        ],
        'related_series' => [
            'name' => 'Related Series',
            'description' => 'Gets properly filled related series list',
            'no_series_message' => 'No related series',
        ],
        'post_list_abstract' => [
            'pagination_group' => 'Pagination',
            'page_parameter_title' => 'Page parameter',
            'page_parameter_description' => 'Calculate pagination based on this URL parameter',
            'pagination_per_page_title' => 'Items per page',
            'pagination_per_page_description' => 'How many items (if any) should be displayed per page',
            'links_group' => 'Links',
            'pagination_validation_message' => 'Per page number must be a valid non-negative integer number',
            'filters_group' => 'Filters',
            'include_categories_title' => 'Include categories',
            'include_categories_description' => 'List category ids or slugs separated by comma to include them. This option includes only listed categories. But they still can be dismissed by exception option',
            'except_posts_title' => 'Exclude posts',
            'except_posts_description' => 'List post ids or slugs separated by comma to exclude them from the list',
            'except_categories_title' => 'Exclude categories',
            'except_categories_description' => 'List category ids or slugs separated by comma to exclude their posts from the list',
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
