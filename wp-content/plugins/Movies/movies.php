<?php
/*
Plugin Name: Movies
Plugin URI: https://done.rs
Description: A plugin to create movies post type.
Version: 1.0
Author: Alexa
Author URI: https://done.rs
License: GPL2
*/  

// Plugin Directory Path
define('MOVIES_DIR', untrailingslashit(plugin_dir_path( __FILE__ )));
// Plugin Directory URL
define('MOVIES_URL', untrailingslashit(plugin_dir_url( __FILE__ )));

// Include main php stuff - all filenames
foreach ( glob( MOVIES_DIR . "/includes/*.php" ) as $filename ) {
    require_once $filename;
}

// Loading cpt
new QTest\Movies;


// Register Custom Gutenberg Block Favorite Movie Quotes
add_action( 'init', function () {
    wp_register_script('fmq', MOVIES_URL.'/favorite-movie-quotes.js', [ 'wp-blocks' ] ); 
    
    register_block_type( 
        'movies/favorite-movie-quotes', 
        [
            'editor_script' => 'fmq',
            'render_callback' => 'fmq_movies_some_hash_render_callback'
        ] 
    ); 
} );

function fmq_movies_some_hash_render_callback( $attributes) {
    ob_start();
    if(isset($attributes['quote'])) // Do not show anything if there is no quote
        echo "<p>Your fav movie qoute is: " . $attributes['quote'] . "</p>";
    return ob_get_clean();
}
