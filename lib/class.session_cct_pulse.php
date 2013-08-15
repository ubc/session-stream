<?php
class Session_CCT_Pulse {
	
	public static function init() {
		if ( Session_CCT::$plugins['pulse_cpt'] ) {
			add_action( 'init', array( __CLASS__, 'load' ), 15 );
		}
	}
	
	public static function load() {
		global $post;
		
		add_action( 'publish_pulse-cpt', array( __CLASS__, 'modify_pulse' ) );
		add_filter( 'the_pulse_data', array( __CLASS__, 'the_pulse_data' ) );
		
		$pulse_query = new WP_Query( Pulse_CPT_Form_Widget::query_arguments() );
		while ( $pulse_query->have_posts() ) {
			$pulse_query->the_post();
			$pulse_data[] = Pulse_CPT::the_pulse_array();
		}
		
		// Reset Post Data
		wp_reset_postdata();
		
		wp_localize_script( 'scct-view', 'pulse_data', $pulse_data );
	}
	
	public static function modify_pulse( $pulse_id ) {
		if ( isset( $_POST['ss_synctime'] ) ) {
			update_post_meta( $pulse_id, "synctime", $_POST['ss_synctime'] );
		}
	}
	
	public static function the_pulse_data( $data ) {
		$synctime = (int) get_post_meta( $data['ID'], "synctime", true );
		
		if ( $synctime > 0 ) {
			$data['date'] = Session_CCT_View::seconds_to_string( $synctime, 3 );
		} else {
			$data['date'] = "";
		}
		
		$data['synctime'] = $synctime;
		$data['reply_to'] = "";
		
		return $data;
	}
}

Session_CCT_Pulse::init();