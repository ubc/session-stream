<?php

/**
 * 
 * 
 */
class Session_CCT_View {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load' ) );
		add_filter( 'scct_load_style', 	 array( __CLASS__, 'load_styles') ); 
    	wp_register_script( 'scct-view', SESSION_CCT_DIR_URL.'/js/view.js', array( 'jquery', 'popcornjs', 'magnific-popup' ), '1.0', true );
    	wp_register_style( 'scct-view', SESSION_CCT_DIR_URL.'/css/view.css' );
    	wp_register_style( 'scct-icons', SESSION_CCT_DIR_URL.'/icons/genericons.css' );
    	wp_register_style( 'scct-layout-desktop', SESSION_CCT_DIR_URL.'/css/layout-desktop.css' );
    	wp_register_style( 'scct-layout-mobile', SESSION_CCT_DIR_URL.'/css/layout-mobile.css' );
	}

	public static function load_styles(){

		Session_CCT_Module::wp_enqueue_style( 'scct-view' );
		Session_CCT_Module::wp_enqueue_style( 'scct-icons' );
    	#Session_CCT_Module::wp_enqueue_style( 'scct-layout-mobile' );
		#Session_CCT_Module::wp_enqueue_style( 'scct-layout-desktop' );
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
    	
		?>
		<div id="header">
			<div class="half">
			<h1 class="site-title"><span class="genericon genericon-reply-single"></span> <a href="<?php echo site_url( ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<?php the_title( "<h2>", "</h2>", true); ?>
			<div class="more-session">
				<div class="nav-previous"><?php previous_post_link('%link', '<span class="genericon genericon-leftarrow"></span> Older Session ') ?></div>
				<div class="nav-next"><?php next_post_link('%link', 'Newer Session <span class="genericon genericon-rightarrow"></span>') ?></div>
			</div>
			</div>

			<div class="half">
				<ul class="nav">
					<li class="mobile-hidden"><a href="#fullscreen" id="fullscreen"><i class="genericon genericon-fullscreen"></i> <span>Full Screen</span></a></li>
					<?php if( class_exists('SCCT_Comments') && comments_open() ) { ?>
						<li><a href="#comments" class="open-popup-link"><i class="genericon genericon-chat"></i> <span>Discussion</span></a></li>
					<?php } 
					if( class_exists( 'SCCT_Module_Bookmarks' ) ) {
						global $scct_module_bookmarks;
						$scct_module_bookmarks->view_bookmarks();
					} 
					?>
				</ul>
			</div>
		</div>
		<div id="timeline" style="display:none;">	
			<a href="#play" id="play-media"><span class="genericon genericon-play"></span></a>
			<div id="timeline-shell">
				<div id="timeline-time">0:00</div>
				<div id="timeline-fill"></div>
			</div>
			<div id="total-time">loading</div>

			<div class="comment-shell" data-time="1:30">
				<img src="http://1.gravatar.com/avatar/b248e2d8d7c239963374add656dec92f?s=26&d=http%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D26&r=G" height="16" width="16" />
				<span>enej</span>
			</div>
			<div class="comment-shell" data-time="2:22">
				<img src="http://1.gravatar.com/avatar/b248e2d8d7c239963374add656dec92f?s=26&d=http%3A%2F%2F1.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D26&r=G" height="16" width="16" />
				<span>Really longname123123</span>
			</div>
			
		</div>
		<div class="session-cct <?php echo implode( " ", apply_filters( "scct_classes", array() ) ); ; ?>">
			<?php do_action( "scct_print_view", $post->ID ); ?>
		</div>
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