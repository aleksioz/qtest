<?php

/**
 * Create Movie cpt. 
 */

namespace QTest;

defined('ABSPATH') or die('safety protocol!');

class Movies{

    public function __construct(){
        add_action('init', [$this, 'register_movies_cpt']);
    }

    public function register_movies_cpt(){
        register_post_type( 'movie', 
            [
                'label' => 'Movie',
                'slug'  => 'movie',
                'public' => true,
                'capability_type' => 'post',
                'rewrite' => ['pages' => false, 'slug' => 'movie'],
                'supports'  => ['editor'], 
                'menu_icon' => 'dashicons-editor-video',
                'menu_position' => 5,
                'rest_base' => 'movie',
                'rest_controller_class' => 'WP_REST_Posts_Controller', 
                'show_in_rest' => true
            ]
        );

        // Register rest field to be exposed in REST calls 
        register_rest_field( 'movie', 'movie_title', 
            [
                'get_callback' => [$this, 'get_movie_title'],
                'update_callback' => null,
                'schema' => null
            ]
        );
    }

    public function get_movie_title($object, $field_name, $request){
        return get_post_meta( $object['id'], 'movie_title', true );
    }


}

new Movies();