<?php

/**
 * Plugin Name: Custom Article API
 * Description: A WordPress plugin that exposes article management through REST API for Laravel integration
 * Version: 1.0.0
 * Author: Diptesh
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register Custom Post Type
function joysuperman_register_article_post_type()
{
    $labels = array(
        'name'                  => 'Articles',
        'singular_name'         => 'Article',
        'menu_name'             => 'Articles',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Article',
        'edit_item'             => 'Edit Article',
        'new_item'              => 'New Article',
        'view_item'             => 'View Article',
        'search_items'          => 'Search Articles',
        'not_found'             => 'No articles found',
        'not_found_in_trash'    => 'No articles found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'article'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => null,
        'supports'            => array('title', 'editor', 'author', 'thumbnail', 'excerpt'),
        'show_in_rest'        => true,
        'rest_base'           => 'articles'
    );

    register_post_type('article', $args);
}
add_action('init', 'joysuperman_register_article_post_type');

// Register REST API endpoints
function joysuperman_register_rest_routes()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-joysuperman-article-api-controller.php';
    $controller = new Joysuperman_Article_API_Controller();
    $controller->register_routes();
}
add_action('rest_api_init', 'joysuperman_register_rest_routes');

// Add authentication check
function joysuperman_check_authentication($request)
{
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            'You are not authorized to access this endpoint.',
            array('status' => 401)
        );
    }
    return true;
}
