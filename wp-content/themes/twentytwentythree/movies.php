<?php
	/**
	*
	* Template Name: Movies
	* @author Alexa
	*
	*/
?>
<?php get_header(); ?>

<?php

// WP_Query all movies from cpt Movies
$movies = new WP_Query([
    'post_type' => 'movie',
    'posts_per_page' => -1
]);

?>

<div class="container">
    <?php if ( $movies->have_posts() ) : ?>
        <?php while ( $movies->have_posts() ) : $movies->the_post(); ?>

            <div style="border: 1px solid #000; padding: 10px; margin: 10px;">
                <h5><?= get_post_meta( get_the_ID(), 'movie_title', true); ?></h5>
                <p><?php the_content(); ?></p>
            </div>
            
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
