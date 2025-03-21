<?php

class Joysuperman_Article_API_Controller extends WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = 'custom-api/v1';
        $this->rest_base = 'article';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(true)
            )
        ));

        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => function ($param) {
                            return is_numeric($param);
                        }
                    ),
                )
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(false)
            )
        ));
    }

    public function create_item_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_forbidden', 'Sorry, you must be logged in to create articles.', array('status' => 401));
        }
        return current_user_can('edit_posts');
    }

    public function get_item_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_forbidden', 'Sorry, you must be logged in to view articles.', array('status' => 401));
        }
        return current_user_can('read');
    }

    public function update_item_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_forbidden', 'Sorry, you must be logged in to update articles.', array('status' => 401));
        }
        return current_user_can('edit_posts');
    }

    public function create_item($request)
    {
        $article = $this->prepare_item_for_database($request);

        $post_id = wp_insert_post($article, true);
        if (is_wp_error($post_id)) {
            return new WP_Error('rest_cannot_create', $post_id->get_error_message(), array('status' => 500));
        }

        $post = get_post($post_id);
        $response = $this->prepare_item_for_response($post, $request);
        return rest_ensure_response($response);
    }

    public function get_item($request)
    {
        $post = get_post($request['id']);

        if (empty($post) || $post->post_type !== 'article') {
            return new WP_Error('rest_post_invalid_id', 'Invalid article ID.', array('status' => 404));
        }

        $response = $this->prepare_item_for_response($post, $request);
        return rest_ensure_response($response);
    }

    public function update_item($request)
    {
        $valid_post = get_post($request['id']);

        if (empty($valid_post) || $valid_post->post_type !== 'article') {
            return new WP_Error('rest_post_invalid_id', 'Invalid article ID.', array('status' => 404));
        }

        $post = $this->prepare_item_for_database($request);
        $post['ID'] = $request['id'];

        $post_id = wp_update_post($post, true);
        if (is_wp_error($post_id)) {
            return new WP_Error('rest_cannot_update', $post_id->get_error_message(), array('status' => 500));
        }

        $post = get_post($post_id);
        $response = $this->prepare_item_for_response($post, $request);
        return rest_ensure_response($response);
    }

    protected function prepare_item_for_database($request)
    {
        $prepared_post = array();

        if (isset($request['title'])) {
            $prepared_post['post_title'] = $request['title'];
        }

        if (isset($request['content'])) {
            $prepared_post['post_content'] = $request['content'];
        }

        if (isset($request['excerpt'])) {
            $prepared_post['post_excerpt'] = $request['excerpt'];
        }

        if (isset($request['status'])) {
            $prepared_post['post_status'] = $request['status'];
        }

        $prepared_post['post_type'] = 'article';

        return $prepared_post;
    }

    public function prepare_item_for_response($post, $request)
    {
        return array(
            'id'      => $post->ID,
            'title'   => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status'  => $post->post_status,
            'date'    => $post->post_date
        );
    }
}
