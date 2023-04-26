<?php

/*
  Custom fields Meta box on movie custom post type
  @see: https://www.youtube.com/watch?v=6VDpvGTUa6A&t=396s/
*/

namespace QTest;

defined('ABSPATH') or die('safety protocol!');

class MovieMetaBox {
  
  public function __construct() {

    add_action( 'add_meta_boxes', [$this, 'addMetabox'] );
    add_action( 'save_post', [$this, 'saveMetabox'] );

    // Column list mod for this metabx
    add_filter( 'manage_movie_posts_columns', [$this, 'add_column'] );
    add_action( 'manage_movie_posts_custom_column', [$this, 'add_movie_to_column'], 10, 2 );

  }




  /**
   * Adds the meta box container.
   */
  public function addMetabox() {
    add_meta_box( 'movie_title', 'Movie Title', [$this, 'renderMetabox'], 'movie', 'normal');
  }


  /**
   * Render Meta Box content.
   *
   * @param WP_Post $post The post object.
   */
  public function renderMetabox($post) {

    // Add nonce for security and authentication.
    wp_nonce_field( 'movie_nonce_action', 'movie_nonce' );

    // Retrieve an existing value from the database.
    $movie_title = get_post_meta( $post->ID, 'movie_title', true );
    
    // Set default values.
    if( empty( $movie_title ) ) $movie_title = '';
  
    // Form fields.
    echo <<<MOVIE_TITLE_FORM
              <table>
                  <tr>
                      <td>
                          <input type="text" id="movie_title" name="movie_title" placeholder="$movie_title" value="$movie_title">
                      </td>
                  </tr>
              </table>
    MOVIE_TITLE_FORM;
  }
  

  /**
   * Save the meta when the post is saved.
   *
   * @param int $post_id The ID of the post being saved.
   */
  public function saveMetabox( $post_id ) {

    // Check if a nonce is valid.
    if ( !isset( $_POST['movie_nonce'] ) || !wp_verify_nonce( $_POST['movie_nonce'], 'movie_nonce_action' ) )
      return;

    // Check if the user has permissions to save data.
    if ( ! current_user_can( 'edit_post', $post_id ) )
      return;

    // Check if it's not an autosave.
    if ( wp_is_post_autosave( $post_id ) )
      return;

    // Sanitize user input.
    $movie_title = isset( $_POST[ 'movie_title' ] ) ? sanitize_text_field( $_POST[ 'movie_title' ] ) : '';
    
    // Update the meta field in the database.
    update_post_meta( $post_id , 'movie_title', $movie_title );

  }







  // Helpers to modify column list - in post list
  public function add_movie_to_column( $column, $post_id ) {
    if ( $column === 'movie_title' ) {
        $meta_value = get_post_meta( $post_id, 'movie_title', true );
        echo $meta_value;
    }
  }
  public function add_column( $columns ) {
    $columns['movie_title'] = 'Movie Title';
    unset($columns['title']);
    return $columns;
  }


}



new MovieMetaBox;