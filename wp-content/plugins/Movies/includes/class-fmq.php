<?php

/**
 * Favorite movie quote guten block. 
 */

namespace QTest;

defined('ABSPATH') or die('safety protocol!');

class Fmq {

    public function __construct(){
        add_action( 'init', [$this, 'register_fmq_block'] );
    }

    public function register_fmq_block(){
        wp_register_script('fmq', MOVIES_URL.'/js/favorite-movie-quotes.js', [ 'wp-blocks' ] ); 
        
        register_block_type( 
            'movies/favorite-movie-quotes', 
            [
                'editor_script' => 'fmq',
                'render_callback' => [$this, 'fmq_render_callback']
            ] 
        ); 
    }

    public function fmq_render_callback( $attributes) {
        ob_start();
        if(isset($attributes['quote'])) // Do not show anything if there is no quote
            echo "<p>Your fav movie qoute is: " . $attributes['quote'] . "</p>";
        return ob_get_clean();
    }


}

new Fmq();