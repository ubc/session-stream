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
	
	public static function register_scripts_and_styles() {
		/*
		ob_start();
		self::slide_meta();
		wp_localize_script( 'scct-admin', 'scct_slide_html', ob_get_clean() );
		
		ob_start();
		self::bookmark_meta();
		wp_localize_script( 'scct-admin', 'scct_bookmark_html', ob_get_clean() );
		
		ob_start();
		self::question_meta();
		wp_localize_script( 'scct-admin', 'scct_question_html', ob_get_clean() );
		*/
	}
	
	/*
	public static function save_post_meta( $post_id, $post_object ) {
		global $meta_box, $wpdb;
		
		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
			return;
		endif;
		
		// We're only interested in Session content.
		if ( $post_object->post_type != 'session-cct' ):
			return;
		endif;
	  
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ):
			return $post_id;
		endif;
		
		$slides = array(
			'offset' => $_POST['slide_meta']['offset'],
			'list'   => array(),
		);
		
		$slide = null;
		foreach ( $_POST['slides'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( $key == 'type' ) {
				if ( ! empty( $slide ) ) {
					$slides['list'][] = $slide;
				}
				
				$slide = array();
			}
			
			$slide[$key] = $value;
		}
		$slides['list'][] = $slide;
		
		$bookmark = null;
		foreach ( $_POST['bookmarks'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( $key == 'title' ) {
				if ( ! empty( $bookmark ) ) {
					$bookmarks['list'][] = $bookmark;
				}
				
				$bookmark = array();
			}
			
			$bookmark[$key] = $value;
		}
		$bookmarks['list'][] = $bookmark;
		
		$questions = array(
			'mode'   => $_POST['question_meta']['mode'],
			'random' => $_POST['question_meta']['random'] == "on",
			'list'   => array(),
		);
		
		$question = null;
		$answer = null;
		foreach ( $_POST['questions'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( self::starts_with( $key, 'answer' ) ) {
				$key = substr( $key, 7 );
				
				if ( $key == 'title' ) {
					if ( ! empty( $answer ) ) {
						$question['answers'][] = $answer;
					}
					
					$answer = array();
				}
				
				$answer[$key] = $value;
			} else {
				if ( $key == 'title' ) {
					if ( ! empty( $question ) ) {
						if ( ! empty( $answer ) ) {
							$question['answers'][] = $answer;
						}
						
						$questions['list'][] = $question;
					}
					
					$question = array();
				}
				
				$question[$key] = $value;
			}
		}
		
		$question['answers'][] = $answer;
		$questions['list'][] = $question;
		
		$pulse = array_merge( array(
			'title'                  => '',
			'display_title'          => false,
			'compact_view'           => true,
			'placeholder'            => "",
			'enable_character_count' => true,
			'num_char'               => 140,
			'enable_url_shortener'   => false,
			'bitly_user'             => get_option( 'pulse_bitly_username' ),
			'bitly_api_key'          => get_option( 'pulse_bitly_key' ),
			'rating_metric'          => false,
			'display_content_rating' => false,
			'enable_replies'         => false,
			'tabs'                   => array(
				'tagging'      => false,
				'co_authoring' => false,
				'file_upload'  => false,
			),
		), $_POST['pulse'] );
		
		update_post_meta( $post_id, 'session_cct_slides',    $slides );
		update_post_meta( $post_id, 'session_cct_bookmarks', $bookmarks );
		update_post_meta( $post_id, 'session_cct_media',     $_POST['media'] );
		update_post_meta( $post_id, 'session_cct_pulse',     $pulse );
		update_post_meta( $post_id, 'session_cct_questions', $questions );
		
		return true;
	}
	*/
	
	function starts_with( $haystack, $needle ) {
		return $needle === "" || strpos( $haystack, $needle ) === 0;
	}
	
}

Session_CCT_Admin::init();