<?php
class Session_CCT_View {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load' ) );
		
    	wp_register_script( 'scct-view', SESSION_CCT_DIR_URL.'/js/view.js',  array( 'jquery', 'popcornjs' ), '1.0', true );
    	wp_register_style(  'scct-view', SESSION_CCT_DIR_URL.'/css/view.css' );
	}
	
	public static function load() {
		add_filter( 'the_content',     array( __CLASS__, 'the_content'      ) );
		add_filter( 'single_template', array( __CLASS__, 'session_template' ) );
		add_filter( 'body_class',      array( __CLASS__, 'edit_body_class'  ), 10, 2);
	}
	
	public static function is_active() {
		return is_single() && get_post_type() == SESSION_CCT_SLUG;
	}
	
	function edit_body_class( $wp_classes, $extra_classes ) {
		if ( Session_CCT_View::is_active() ) {
			$wp_classes[] = "full-width";
		}
		
		return array_merge( $wp_classes, (array) $extra_classes );
	}
	
	function session_template( $template ) {
		if ( Session_CCT_View::is_active() ) {
			$template = SESSION_CCT_DIR_PATH.'/view/session.php';
		}
		
		return $template;
	}
	
	public static function the_content( $content ) {
		if ( Session_CCT_View::is_active() ) {
			return self::the_session( $content );
		}
	}
	
	public static function the_session( $content ) {
		global $post;
		
		wp_localize_script( 'scct-view', 'scct_data', apply_filters( "scct_localize_view", array() ) );
		
    	wp_enqueue_script( 'scct-view' );
    	wp_enqueue_style( 'scct-view' );
		
		ob_start();
		do_action( "scct_print_view", $post->ID );
		return ob_get_clean();
		
		/*
		$bookmarks = get_post_meta( $post->ID, 'session_cct_bookmarks', true );
		$questions = get_post_meta( $post->ID, 'session_cct_questions', true );
		$media     = get_post_meta( $post->ID, 'session_cct_media',     true );
		$slides    = get_post_meta( $post->ID, 'session_cct_slides',    true );
		$pulse     = get_post_meta( $post->ID, 'session_cct_pulse',     true );
		
		foreach ( $slides['list'] as $index => $slide ) {
			if ( ! empty( $slide['content'] ) ) {
				$slides['list'][$index]['content'] = do_shortcode( $slide['content'] );
			}
			
			$slides['list'][$index]['start'] = self::string_to_seconds( $slide['start'] );
		}
		
		foreach ( $bookmarks['list'] as $index => $bookmark ) {
			$bookmarks['list'][$index]['synctime'] = self::string_to_seconds( $bookmark['time'] );
		}
		
		foreach ( $questions['list'] as $index => $question ) {
			$questions['list'][$index]['synctime'] = self::string_to_seconds( $question['time'] );
		}
		
		wp_localize_script( 'scct-view', 'scct_question_template', self::question_template() );
		wp_localize_script( 'scct-view', 'session_data', array(
			'media'     => $media,
			'slides'    => $slides,
			'bookmarks' => $bookmarks,
			'questions' => $questions,
		) );
		self::enqueue_scripts_and_styles();
		
		ob_start();
		?>
		<div class="questions-wrapper">
			<div id="scct-questions"></div>
		</div>
		<?php if ( $pulse['status'] != 'disabled' ): ?>
			<div class="pulse-wrapper widget">
				<div id="scct-pulse-list" class="pulse-widget">
					<?php
						if ( $pulse['status'] != 'locked' ) {
							Pulse_CPT_Form_Widget::pulse_form( $pulse );
						}
					?>
					<div id="pulse-list" class="pulse-list">
						<!-- To be populated by PopcornJS -->
					</div>
				</div>
			</div>
		<?php endif; ?>
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
		*/
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