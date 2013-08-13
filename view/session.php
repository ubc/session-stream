<?php wp_head(); ?>
<?php //get_header(); ?>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="session-cct">
				<?php the_content(); ?>
			</div>
		<?php endwhile; // end of the loop. ?>
	</div><!-- #content -->
</div><!-- #primary -->
<?php //get_footer(); ?>
<?php wp_footer(); ?>