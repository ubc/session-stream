<?php

/**
 * 
 * 
 */
class Session_CCT_View {
	
	public static function init() {

		add_action( 'init', array( __CLASS__, 'load' ) );
		add_filter( 'scct_load_style', 	 array( __CLASS__, 'load_styles') ); 

    	
    	wp_register_style( 'normalize', SESSION_CCT_DIR_URL.'/assets/foundation/css/normalize.css' );
    	wp_register_style( 'foundation', SESSION_CCT_DIR_URL.'/assets/foundation/css/foundation.min.css' );
    	wp_register_style( 'scct-view', SESSION_CCT_DIR_URL.'/assets/css/view.css' );
    	wp_register_style( 'scct-icons', SESSION_CCT_DIR_URL.'/assets/icons/genericons.css' );

    	wp_register_script( 'foundation', SESSION_CCT_DIR_URL.'/assets/foundation/js/foundation.min.js', array( 'jquery' ), '5.1', true );
    	wp_register_script( 'popcornjs', SESSION_CCT_DIR_URL.'/assets/js/popcorn-complete.js', array(), '1.3', true );
    	wp_register_script( 'magnific-popup', 	 SESSION_CCT_DIR_URL.'/js/jquery.magnific-popup.min.js', array(), '0.9.9', true );
    	wp_register_script( 'nano-scroller', 	 SESSION_CCT_DIR_URL.'/assets/js/jquery.nanoscroller.min.js', array( 'jquery' ), '0.7.6', true );
    	wp_register_script( 'dotjs',     SESSION_CCT_DIR_URL.'/js/doT.js',                     array(), '1.0', true );
    	wp_register_script( 'scct-view', SESSION_CCT_DIR_URL.'/assets/js/view.js', array( 'jquery', 'popcornjs', 'foundation', 'nano-scroller' ), '1.0', true );
    	
	}

	public static function load_styles() {
		Session_CCT_Module::wp_enqueue_style( 'normalize' );
		Session_CCT_Module::wp_enqueue_style( 'foundation' );
		Session_CCT_Module::wp_enqueue_style( 'scct-icons' );
		Session_CCT_Module::wp_enqueue_style( 'scct-view' );
		
	}
	
	public static function load() {
		add_filter( 'single_template', array( __CLASS__, 'session_template' ) );
		/*
		add_filter( 'the_content',     array( __CLASS__, 'the_content'      ) );
		
		add_filter( 'body_class',      array( __CLASS__, 'edit_body_class'  ), 10, 2);
		*/
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

	public static function the_head(){
		global $post;

		$data = apply_filters( "scct_localize_view", array(
			'session_data' => array( 
				'session_id' 		=> $post->ID,
				'session_title' 	=> $post->post_title,
				'session_permalink' => get_permalink( $post->ID ) ),

			'meta' => array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ) )
		
			) );
		
		wp_localize_script( 'scct-view', 'session_stream_data', $data );
    	wp_enqueue_script( 'scct-view' );

	}
	
	public static function the_session( $content ) {
		global $post;
    	
		?>
		<div id="body-wrap" class="<?php echo implode( " ", apply_filters( "scct_classes", array() ) ) ; ?>" >
			<div id="wrapper" class="main-view ">
				<div id="main">
					<?php do_action( "scct-print-main-view", $post->ID ); ?>
					This is the main shell
				</div>
			</div>

			
		</div>
		<?php
		
	}
	public static function the_navigation() { ?>
			<div class="navigation-content">
				<?php do_action( "scct-print-navigation-view", $post->ID ); ?>
			</div>
		<?php
	}

	public static function the_sidebar() { ?>
		
				<div class="sidebar-content">
				<?php do_action( "scct-print-sidebar-view", $post->ID ); ?>
				</div>

		<?php
	}
	public static function the_header() { 
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
			<?php 
			/*
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
			*/ ?>
		</div>
	<?php
	}

	public static function show_timeline() { ?>
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