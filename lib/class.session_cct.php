<?php

class Session_CCT {
	public static $plugins = array();
	
	public static function init() {

		add_action( 'init', array( __CLASS__, 'load' ) );

		
		self::$plugins['pulse_cpt'] = defined( "PULSE_CPT_BASENAME" );
		self::$plugins['stream'] 	= defined( "CTLT_STREAM" );

		add_action( 'add_meta_boxes', array( __CLASS__, 'remove_comments_meta_boxes' ), 10 );

	}
	
	public static function install() {
		self::register_content_type();
		flush_rewrite_rules();
	}
	
	public static function load() {
		self::register_content_type();
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
			'menu_icon'			 => 'dashicons-video-alt3',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'session' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies'         => array( 'category', 'post_tag' ),
			'supports'           => array( 'title', 'excerpt', 'author', 'comments'  ),
		);
		
  		register_post_type( SESSION_CCT_SLUG, $args );
	}

	public static function remove_comments_meta_boxes() {
		remove_meta_box( 'commentstatusdiv', SESSION_CCT_SLUG, 'normal' );
		remove_meta_box( 'commentsdiv', SESSION_CCT_SLUG, 'normal' );
	}
	
	
	
}

Session_CCT::init();