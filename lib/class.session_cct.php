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
    	wp_register_script( 'popcornjs',        'http://popcornjs.org/code/dist/popcorn-complete.js', array(), '1.0', true );
    	wp_register_script( 'popcorn-pulse',    SESSION_CCT_DIR_URL.'/js/popcorn.pulse.js',    array( 'popcornjs' ), '1.0', true );
    	wp_register_script( 'popcorn-question', SESSION_CCT_DIR_URL.'/js/popcorn.question.js', array( 'popcornjs' ), '1.0', true );
	}
	
}

Session_CCT::init();