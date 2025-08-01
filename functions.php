<?php

// Enqueue scripts and styles
function mytheme_enqueue_assets()
{
    // React CDN
    wp_enqueue_script('react', 'https://unpkg.com/react@17/umd/react.production.min.js', [], null, true);
    wp_enqueue_script('react-dom', 'https://unpkg.com/react-dom@17/umd/react-dom.production.min.js', ['react'], null, true);

    // Compiled React App
    wp_enqueue_script(
        'my-react-app',
        get_theme_file_uri('/assets/js/my-react-app.js'),
        ['react', 'react-dom'],
        filemtime(get_theme_file_path('/assets/js/my-react-app.js')),
        true
    );

    // Contact Form CSS
    $css_path = get_theme_file_path('/assets/css/react-form.css');
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'react-contact-form',
            get_theme_file_uri('/assets/css/react-form.css'),
            [],
            filemtime($css_path)
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_assets');

// Shortcode to render React div
add_shortcode('render_react', function () {
    return '<div id="react-app"></div>';
});

// Register CPT: Book
function mytheme_register_cpt_books()
{
    register_post_type('book', [
        'labels' => [
            'name'               => 'Books',
            'singular_name'      => 'Book',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Book',
            'edit_item'          => 'Edit Book',
            'new_item'           => 'New Book',
            'view_item'          => 'View Book',
            'search_items'       => 'Search Books',
            'not_found'          => 'No books found',
            'menu_name'          => 'Books',
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'books'],
        'show_in_rest'  => true,
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'menu_icon'     => 'dashicons-book',
    ]);
}
add_action('init', 'mytheme_register_cpt_books');

// Register REST API Endpoint to create Book
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/create-book', [
        'methods'             => 'POST',
        'callback'            => 'mytheme_create_book',
        'permission_callback' => fn() => current_user_can('edit_posts'),
        'args' => [
            'title'        => ['required' => true,  'type' => 'string'],
            'content'      => ['required' => false, 'type' => 'string'],
            'publish_year' => ['required' => true,  'type' => 'string'],
            'author_name'  => ['required' => true,  'type' => 'string'],
        ]
    ]);
});

// REST Callback
function mytheme_create_book($request)
{
    $title        = sanitize_text_field($request['title']);
    $content      = wp_kses_post($request['content']);
    $publish_year = sanitize_text_field($request['publish_year']);
    $author_name  = sanitize_text_field($request['author_name']);

    $post_id = wp_insert_post([
        'post_type'    => 'book',
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return new WP_Error('create_failed', 'Failed to create book.', ['status' => 500]);
    }

    update_post_meta($post_id, 'publish_year', $publish_year);
    update_post_meta($post_id, 'author_name', $author_name);

    return new WP_REST_Response(['success' => true, 'post_id' => $post_id], 200);
}
