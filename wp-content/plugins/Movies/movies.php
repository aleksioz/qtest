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

// Include main php stuff - all filenames
foreach ( glob( MOVIES_DIR . "/includes/*.php" ) as $fn ) {
    require_once $fn;
}

// Loading cpt
new QTest\Movies;

// Handle Admin menu
// new QTest\AdminMenu;