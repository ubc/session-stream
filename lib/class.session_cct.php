<?php

class Session_CCT {
	public static $plugins = array();
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load' ) );
		
		self::$plugins['pulse_cpt'] = defined( "PULSE_CPT_BASENAME" );
		self::$plugins['stream'] = defined( "CTLT_STREAM" );
	}
	
	public static function install() {
		self::register_content_type();
		flush_rewrite_rules();
	}
	
	public static function load() {
		self::register_content_type();
		self::register_scripts();
	}
	
	public static function register_content_type() {
		$labels = array(
			'name'               => _x( 'Session', 'session-cct' ),
			'singular_name'      => _x( 'Session', 'session-cct' ),
			'add_new'            => _x( 'Add New', 'session-cct' ),
			'add_new_item'       => __( 'Add New Session' ),
			'edit_item'          => __( 'Edit Session' ),
			'new_item'           => __( 'New Session' ),
			'all_items'          => __( 'All Sessions' ),
			'view_item'          => __( 'View Session' ),
			'search_items'       => __( 'Search Sessions' ),
			'not_found'          => __( 'No sessions found' ),
			'not_found_in_trash' => __( 'No sessions found in Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'Session',
		);
		
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'session' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies'         => array( 'category', 'post_tag' ),
			'supports'           => array( 'title', 'excerpt', 'author' ),
		);
		
  		register_post_type( SESSION_CCT_SLUG, $args );
	}
	
	public static function register_scripts() {
    	wp_register_script( 'popcornjs', SESSION_CCT_DIR_URL.'/js/popcorn-complete.js', array(), '1.0', true );
    	wp_register_script( 'dotjs',     SESSION_CCT_DIR_URL.'/js/doT.js',                     array(), '1.0', true );
	}
	
	public static function is_mobile() {
		$mobile_browser = '0';
		
		if ( preg_match( '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower( $_SERVER['HTTP_USER_AGENT'] ) ) ) {
			$mobile_browser++;
		}
		
		if ( ( strpos( strtolower( $_SERVER['HTTP_ACCEPT'] ), 'application/vnd.wap.xhtml+xml' ) > 0 ) or ( ( isset( $_SERVER['HTTP_X_WAP_PROFILE'] ) or isset( $_SERVER['HTTP_PROFILE'] ) ) ) ) {
			$mobile_browser++;
		}    
		
		$mobile_ua = strtolower( substr( $_SERVER['HTTP_USER_AGENT'], 0, 4 ) );
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-'
		);
		
		if ( in_array( $mobile_ua, $mobile_agents ) ) {
			$mobile_browser++;
		}
		
		if ( strpos( strtolower( $_SERVER['ALL_HTTP'] ), 'OperaMini' ) > 0) {
			$mobile_browser++;
		}
		
		if ( strpos( strtolower( $_SERVER['HTTP_USER_AGENT'] ), 'windows' ) > 0) {
			$mobile_browser = 0;
		}
		
		return $mobile_browser;
	}
	
}

Session_CCT::init();