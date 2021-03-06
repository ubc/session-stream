<?php
class Session_CCT_View {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load' ) );
		
    	wp_register_script( 'scct-view', SESSION_CCT_DIR_URL.'/js/view.js', array( 'jquery', 'popcornjs' ), '1.0', true );
    	wp_register_style( 'scct-view', SESSION_CCT_DIR_URL.'/css/view.css' );
    	wp_register_style( 'scct-layout-desktop', SESSION_CCT_DIR_URL.'/css/layout-desktop.css' );
    	wp_register_style( 'scct-layout-mobile', SESSION_CCT_DIR_URL.'/css/layout-mobile.css' );
	}
	
	public static function load() {
		add_filter( 'the_content',     array( __CLASS__, 'the_content'      ) );
		add_filter( 'single_template', array( __CLASS__, 'session_template' ) );
		add_filter( 'body_class',      array( __CLASS__, 'edit_body_class'  ), 10, 2);
	}
	
	public static function is_active() {
		return is_single() && get_post_type() == SESSION_CCT_SLUG;
	}
	
	static function edit_body_class( $wp_classes, $extra_classes ) {
		if ( Session_CCT_View::is_active() ) {
			$wp_classes[] = "full-width";
		}
		
		return array_merge( $wp_classes, (array) $extra_classes );
	}
	
	static function session_template( $template ) {
		if ( Session_CCT_View::is_active() ) {
			$template = SESSION_CCT_DIR_PATH.'/view/session.php';
		}
		
		return $template;
	}
	
	public static function the_content( $content ) {
		if ( Session_CCT_View::is_active() ) {
			return self::the_session( $content );
		}
        else {
            return $content;
        }
	}
	
	public static function the_session( $content ) {
		global $post;
		
		$data = apply_filters( "scct_localize_view", array(
			'session_id' => $post->ID,
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
		
		wp_localize_script( 'scct-view', 'scct_data', $data );
		
    	wp_enqueue_script( 'scct-view' );
    	wp_enqueue_style( 'scct-view' );
    	wp_enqueue_style( 'scct-layout-mobile' );
		wp_enqueue_style( 'scct-layout-desktop' );
		
		ob_start();
		?>
		<div class="session-cct <?php echo implode( " ", apply_filters( "scct_classes", array() ) ); ; ?>">
			<?php do_action( "scct_print_view", $post->ID ); ?>
		</div>
		<?php
		return ob_get_clean();
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