<?php
/*
Plugin Name: Movies
Plugin URI: https://done.rs
Description: A plugin to create movies post type.
Version: 1.0.1
Author: Alexa
Author URI: https://done.rs
License: GPL2
*/  

// Plugin Directory Path
define('MOVIES_DIR', untrailingslashit(plugin_dir_path( __FILE__ )));
// Plugin Directory URL
define('MOVIES_URL', untrailingslashit(plugin_dir_url( __FILE__ )));



// Include main php stuff - all filenames - loads all instances
foreach ( glob( MOVIES_DIR . "/includes/*.php" ) as $filename ) {
    require_once $filename;
}
