<?php wp_head(); ?>
<?php //get_header(); ?>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		?>
	</div><!-- #content -->
</div><!-- #primary -->
<?php //get_footer(); ?>
<?php wp_footer(); ?>