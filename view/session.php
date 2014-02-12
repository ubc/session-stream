<?php
/**
* The Header for our theme.
*
* Displays all of the <head> section and everything up till <div id="content">
*
* @package _s
*/
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '|', true, 'right' ); ?></title>

<?php do_action( 'scct_load_style' ); ?>
</head>
<body <?php body_class(); ?>>
<?php //get_header(); ?>

	<div id="content" role="main">
		
		<?php 
		if ( have_posts() ) : while ( have_posts() ) : the_post(); 
		Session_CCT_View::the_session( '' );
		endwhile; ?>
		<?php endif; ?>
	</div><!-- #content -->

<?php //get_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>