<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" <?php language_attributes(); ?> >

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="<?php bloginfo( 'charset' ); ?>">

		<title><?php wp_title( '|', true, 'right' ); ?></title>
		<?php do_action( 'scct_load_style' ); ?>
		

	<script src="<?php echo SESSION_CCT_DIR_URL;?>/assets/foundation/js/vendor/modernizr.js"></script>
	</head>
	<body <?php body_class(); ?>>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post();  ?>
		<?php Session_CCT_View::the_head(); 
		
		?>
		<div class="page-wrap">
			<div class="off-canvas-wrap"> 
				<div class="inner-wrap"> 
					<nav class="tab-bar">
						<section class="left-small"> <a class="left-off-canvas-toggle genericon genericon-menu" ><span></span></a></section> 
						<section class="middle tab-bar-section"> <h1 class="title"><?php the_title(); ?></h1> </section> 
						<section class="right-small"> <a class="right-off-canvas-toggle genericon genericon-chat" ><span></span><span class="comment-number	round alert label"><?php echo get_comments_number(); ?></span></a> </section> 
					</nav> 
					<aside class="left-off-canvas-menu"> 
					<ul class="off-canvas-list"> 
					<li><label>Foundation</label></li> 
					<li><a href="#">The Psychohistorians</a></li>
					</ul> 
					<?php Session_CCT_View::the_navigation(); ?>
					</aside> 

					<aside class="right-off-canvas-menu right-sidebar"> 
						
						<?php Session_CCT_View::the_sidebar(); ?>
						
					</aside> 
					<section class="main-section"> 
						<?php Session_CCT_View::the_content( $post ); ?>
					The Psychohistorians

<p>Set in the year 0 F.E. ("Foundation Era"), The Psychohistorians opens on Trantor, the capital of the 12,000-year-old Galactic Empire. Though the empire appears stable and powerful, it is slowly decaying in ways that parallel the decline of the Western Roman Empire. Hari Seldon, a mathematician and psychologist, has developed psychohistory, a new field of science and psychology that equates all possibilities in large societies to mathematics, allowing for the prediction of future events.</p>

<p>Using psychohistory, Seldon has discovered the declining nature of the Empire, angering the aristocratic members of the Committee of Public Safety, the de facto rulers of the Empire. The Committee considers Seldon's views and statements treasonous, and he is arrested along with young mathematician Gaal Dornick, who has arrived on Trantor to meet Seldon. Seldon is tried by the Committee and defends his beliefs, explaining his theories and predictions, including his belief that the Empire will collapse in 500 years and enter a 30,000-year dark age, to the Committee's members.</p>
<p>Set in the year 0 F.E. ("Foundation Era"), The Psychohistorians opens on Trantor, the capital of the 12,000-year-old Galactic Empire. Though the empire appears stable and powerful, it is slowly decaying in ways that parallel the decline of the Western Roman Empire. Hari Seldon, a mathematician and psychologist, has developed psychohistory, a new field of science and psychology that equates all possibilities in large societies to mathematics, allowing for the prediction of future events.</p>

<p>Using psychohistory, Seldon has discovered the declining nature of the Empire, angering the aristocratic members of the Committee of Public Safety, the de facto rulers of the Empire. The Committee considers Seldon's views and statements treasonous, and he is arrested along with young mathematician Gaal Dornick, who has arrived on Trantor to meet Seldon. Seldon is tried by the Committee and defends his beliefs, explaining his theories and predictions, including his belief that the Empire will collapse in 500 years and enter a 30,000-year dark age, to the Committee's members.</p>
<p>Set in the year 0 F.E. ("Foundation Era"), The Psychohistorians opens on Trantor, the capital of the 12,000-year-old Galactic Empire. Though the empire appears stable and powerful, it is slowly decaying in ways that parallel the decline of the Western Roman Empire. Hari Seldon, a mathematician and psychologist, has developed psychohistory, a new field of science and psychology that equates all possibilities in large societies to mathematics, allowing for the prediction of future events.</p>

<p>Using psychohistory, Seldon has discovered the declining nature of the Empire, angering the aristocratic members of the Committee of Public Safety, the de facto rulers of the Empire. The Committee considers Seldon's views and statements treasonous, and he is arrested along with young mathematician Gaal Dornick, who has arrived on Trantor to meet Seldon. Seldon is tried by the Committee and defends his beliefs, explaining his theories and predictions, including his belief that the Empire will collapse in 500 years and enter a 30,000-year dark age, to the Committee's members.</p>
<p>Set in the year 0 F.E. ("Foundation Era"), The Psychohistorians opens on Trantor, the capital of the 12,000-year-old Galactic Empire. Though the empire appears stable and powerful, it is slowly decaying in ways that parallel the decline of the Western Roman Empire. Hari Seldon, a mathematician and psychologist, has developed psychohistory, a new field of science and psychology that equates all possibilities in large societies to mathematics, allowing for the prediction of future events.</p>

<p>Using psychohistory, Seldon has discovered the declining nature of the Empire, angering the aristocratic members of the Committee of Public Safety, the de facto rulers of the Empire. The Committee considers Seldon's views and statements treasonous, and he is arrested along with young mathematician Gaal Dornick, who has arrived on Trantor to meet Seldon. Seldon is tried by the Committee and defends his beliefs, explaining his theories and predictions, including his belief that the Empire will collapse in 500 years and enter a 30,000-year dark age, to the Committee's members.</p>


					</section> 
					<a class="exit-off-canvas"></a> 
				</div> 
			</div>
		</div>
		<?php /*
		<div class="m-pikabu-viewport">
			<!-- the left sidebar -->
			<div class="m-pikabu-sidebar m-pikabu-left">
			<!-- left sidebar content -->
				Left Side
				
			</div>
			<!-- the main page content -->
			<div class="m-pikabu-container">
			<!-- Overlay is needed to have a big click area to close the sidebars -->
			<div class="m-pikabu-overlay"></div>

			<header>
				<h1>Pikabu</h1>
				
				<a class="m-pikabu-nav-toggle" data-role="left">Left Menu</a>
				<a class="m-pikabu-nav-toggle" data-role="right">Right Menu</a>
				<div class="full-width">
				This is a somre really long content
				</div>
			</header>
			<section>
				<?php // Session_CCT_View::the_content(); ?>
			</section>
			</div>
			<!-- the right sidebar -->
			<div class="m-pikabu-sidebar m-pikabu-right">
			<!-- right sidebar content -->
			right side 
			</div>
		</div> 
		<?php
			*/ 
			endwhile; ?>
		<?php endif; ?>


	<?php wp_footer(); ?>
	</body>
</html>