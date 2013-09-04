<?php
class Session_CCT_Admin {
	
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'load' ), 20 );
		
    	wp_register_script( 'scct-admin', SESSION_CCT_DIR_URL.'/js/admin.js', array( 'jquery' ), '1.0', true );
    	wp_register_style(  'scct-admin', SESSION_CCT_DIR_URL.'/css/admin.css' );
	}
	
	public static function load() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box_remove' ), 15 );
		wp_localize_script( 'scct-admin', 'scct_data', apply_filters( "scct_localize_admin", array() ) );
		
    	wp_enqueue_script( 'scct-admin' );
    	wp_enqueue_style( 'scct-admin' );
	}
	
	public static function meta_box_remove() {
		remove_meta_box( 'pulse-post-meta', SESSION_CCT_SLUG, 'side' );
	}
	
	function starts_with( $haystack, $needle ) {
		return $needle === "" || strpos( $haystack, $needle ) === 0;
	}
	
}

Session_CCT_Admin::init();