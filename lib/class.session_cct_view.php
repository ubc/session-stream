<?php

class Session_CCT_View {
	public static $show_session = false;
	
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
    	wp_register_script( 'scct-view',  SESSION_CCT_DIR_URL.'/js/view.js',  array( 'jquery', 'popcornjs' ), '1.0', true );
    	wp_register_script( 'scct-pulse', SESSION_CCT_DIR_URL.'/js/pulse.js', array( 'jquery' ), '1.0', true );
    	wp_register_style( 'scct-view', SESSION_CCT_DIR_URL.'/css/view.css' );
    	wp_register_style( 'scct-view-bookmarks', SESSION_CCT_DIR_URL.'/css/view-bookmarks.css' );
    	wp_register_style( 'scct-view-slideshow', SESSION_CCT_DIR_URL.'/css/view-slideshow.css' );
    	wp_register_style( 'scct-view-media', SESSION_CCT_DIR_URL.'/css/view-media.css' );
	}
	
	public static function enqueue_scripts_and_styles() {
		wp_enqueue_script( 'popcorn-pulse' );
    	wp_enqueue_script( 'scct-view' );
    	wp_enqueue_script( 'scct-pulse' );
    	wp_enqueue_style( 'scct-view' );
    	wp_enqueue_style( 'scct-view-bookmarks' );
    	wp_enqueue_style( 'scct-view-slideshow' );
    	wp_enqueue_style( 'scct-view-media' );
	}
	
	function edit_body_class( $wp_classes, $extra_classes ) {
		if ( get_post_type() == SESSION_CCT_SLUG ) {
			$wp_classes[] = "full-width";
		}
		
		return array_merge( $wp_classes, (array) $extra_classes );
	}
	
	function session_template( $template ) {
		if ( get_post_type() == SESSION_CCT_SLUG ) {
			self::$show_session = true;
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
		$args['posts_per_page'] = -1;
		$args['orderby'] = "";
		
		$pulse_query = new WP_Query( $args );
		
		$bookmarks = get_post_meta( $post->ID, 'session_cct_bookmarks', true );
		$media     = get_post_meta( $post->ID, 'session_cct_media',     true );
		$slides    = get_post_meta( $post->ID, 'session_cct_slides',    true );
		$pulse     = get_post_meta( $post->ID, 'session_cct_pulse',     true );
		
		foreach ( $slides['list'] as $index => $slide ) {
			if ( ! empty( $slide['content'] ) ) {
				$slides['list'][$index]['content'] = do_shortcode( $slide['content'] );
			}
			$slides['list'][$index]['start'] = self::string_to_seconds( $slide['start'] );
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
				<?php Pulse_CPT_Form_Widget::pulse_form( $pulse ); ?>
				<div id="pulse-list" class="pulse-list">
					<!-- To be populated by PopcornJS -->
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
		
		$seconds = self::string_to_seconds( $time );
		$action = "Session_CCT_View.skipTo(".$seconds.");";
		
		?>
		<li class="control" title="<?php echo $title; ?>">
			<a class="scct-bookmark" onclick="<?php echo $action; ?>">
				<?php echo $title; ?><span class="timestamp"><?php echo $time; ?></span>
			</a>
		</li>
		<?php
	}
	
	public static function string_to_seconds( $string ) {
		$seconds = 0;
		$segments = array_reverse( split( ':', $string ) );
		$increments = array( 1, MINUTE_IN_SECONDS, HOUR_IN_SECONDS );
		
		for ( $i = 0; $i < count( $segments ); $i++ ) {
			$seconds += $segments[$i] * $increments[$i];
		}
		
		return $seconds;
	}
	
	public static function seconds_to_string( $seconds, $zeroes ) {
		$string = "";
		$increments = array( HOUR_IN_SECONDS, MINUTE_IN_SECONDS, 1 );
		$count = count( $increments );
		$segments = array();
		$remainder = $seconds;
		
		for ( $i = 0; $i < $count; $i++ ) {
			if ( $increments[$i] < $remainder ) {
				$segments[$i] = (int) ( $remainder / $increments[$i] );
				$remainder = $remainder % $increments[$i];
			} else if ( $zeroes >= ($count - $i - 1)*2 ) {
				$segments[$i] = 0;
			}
			
			if ( $zeroes >= ($count - $i)*2 + 1 && $segments[$i] < 10 ) {
				$segments[$i] = "0".$segments[$i];
			}
		}
		
		return implode( ":", $segments );
	}
	
}

Session_CCT_View::init();