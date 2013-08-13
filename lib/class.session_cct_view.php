<?php

class Session_CCT_View {
	
	public static function init() {
		add_action( 'init',            array( __CLASS__, 'load'             ) );
		add_filter( 'the_content',     array( __CLASS__, 'the_content'      ) );
		add_filter( 'single_template', array( __CLASS__, 'session_template' ) );
		add_filter( 'body_class',      array( __CLASS__, 'edit_body_class'  ), 10, 2);
	}
	
	public static function load() {
		self::register_scripts_and_styles();
	}
	
	public static function register_scripts_and_styles() {
    	wp_register_script( 'scct-view', SESSION_CCT_DIR_URL.'/js/view.js', array( 'popcornjs', 'jquery' ), '1.0', true );
    	wp_register_style( 'scct-view', SESSION_CCT_DIR_URL.'/css/view.css' );
    	wp_register_style( 'scct-view-bookmarks', SESSION_CCT_DIR_URL.'/css/view-bookmarks.css' );
    	wp_register_style( 'scct-view-slideshow', SESSION_CCT_DIR_URL.'/css/view-slideshow.css' );
    	wp_register_style( 'scct-view-media', SESSION_CCT_DIR_URL.'/css/view-media.css' );
	}
	
	public static function enqueue_scripts_and_styles() {
		wp_enqueue_script( 'popcorn-pulse' );
    	wp_enqueue_script( 'scct-view' );
    	wp_enqueue_style( 'scct-view' );
    	wp_enqueue_style( 'scct-view-bookmarks' );
    	wp_enqueue_style( 'scct-view-slideshow' );
    	wp_enqueue_style( 'scct-view-media' );
	}
	
	function edit_body_class( $wp_classes, $extra_classes ) {
		global $post;
	   
		if ( $post->post_type == SESSION_CCT_SLUG ) {
			$wp_classes[] = "full-width";
		}
		
		return array_merge( $wp_classes, (array) $extra_classes );
	}
	
	function session_template( $template ) {
		global $post;
	   
		if ( $post->post_type == SESSION_CCT_SLUG ) {
			$template = SESSION_CCT_DIR_PATH.'/view/session.php';
		}
		
		return $template;
	}
	
	public static function the_content( $content ) {
		if ( get_post_type() == SESSION_CCT_SLUG && is_single() ) {
			return self::the_session( $content );
		}
	}
	
	public static function the_session( $content ) {
		global $post;
		
		$args = Pulse_CPT_Form_Widget::query_arguments();
		
		
		$pulse_query = new WP_Query( $args );
		
		$bookmarks = get_post_meta( $post->ID, 'session_cct_bookmarks', true );
		$media = get_post_meta( $post->ID, 'session_cct_media', true );
		$slides = get_post_meta( $post->ID, 'session_cct_slides', true );
		
		foreach ( $slides['list'] as $index => $slide ) {
			if ( ! empty( $slide['content'] ) ) {
				$slides['list'][$index]['content'] = do_shortcode( $slide['content'] );
			}
		}
		
		wp_localize_script( 'scct-view', 'session_data', array(
			'media'  => $media,
			'slides' => $slides,
		) );
		self::enqueue_scripts_and_styles();
		
		ob_start();
		?>
		<div class="pulse-wrapper widget">
			<div id="scct-pulse-list" class="pulse-widget">
				<?php Pulse_CPT_Form_Widget::pulse_form(); ?>
				<div class="pulse-list">
					<?php
						while ( $pulse_query->have_posts() ):
							$pulse_query->the_post();
							Pulse_CPT::the_pulse( Pulse_CPT::the_pulse_array( $instance ), false, false );
						endwhile;
						
						// Reset Post Data
						wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
		<div class="bookmarks-wrapper">
			<ul id="scct-bookmarks">
				<li class="title">
					Bookmarks
				</li>
				<?php
					foreach ( $bookmarks['list'] as $bookmark ) {
						self::bookmark( $bookmark );
					}
				?>
			</ul>
		</div>
		<div class="media-wrapper">
			<div id="scct-media" class="iframe-wrapper <?php echo $media['type']; ?>"></div>
		</div>
		<div class="slideshow-wrapper">
			<div id="scct-slide" class="slide"></div>
		</div>
		<?php
		Pulse_CPT_Form_Widget::footer( $instance );
		return ob_get_clean();
	}
	
	public static function bookmark( $data = array() ) {
		$title = $data['title'];
		$time = $data['time'];
		
		$seconds = 0;
		$segments = array_reverse( split( ':', $time ) );
		$increments = array( 1, MINUTE_IN_SECONDS, HOUR_IN_SECONDS );
		
		for ( $i = 0; $i < count( $segments ); $i++ ):
			$seconds += $segments[$i] * $increments[$i];
		endfor;
		
		$action = "Session_CCT_View.skipTo(".$seconds.");";
		
		?>
		<li class="control" title="<?php echo $title; ?>">
			<a class="scct-bookmark" onclick="<?php echo $action; ?>">
				<?php echo $title; ?><span class="timestamp"><?php echo $time; ?></span>
			</a>
		</li>
		<?php
	}
	
}

Session_CCT_View::init();