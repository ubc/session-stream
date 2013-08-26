<?php
class SCCT_Module_Pulse extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( "Pulse", "default", "side" );
		
    	wp_register_style(  'scct-view-pulse', SESSION_CCT_DIR_URL.'/module/pulse/view-pulse.css' );
    	wp_register_script( 'scct-view-pulse', SESSION_CCT_DIR_URL.'/module/pulse/view-pulse.js', array( 'jquery' ), '1.0', true );
    	wp_register_script( 'popcornjs-pulse', SESSION_CCT_DIR_URL.'/module/pulse/popcorn.pulse.js', array( 'jquery', 'popcornjs' ), '1.0', true );
		
		add_action( 'publish_pulse-cpt', array( __CLASS__, 'modify_pulse' ) );
	}
	
	public function load_view() {
		add_filter( 'the_pulse_data', array( $this, 'the_pulse_data' ) );
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}
	
	public function admin( $post, $box ) {
		$data = get_post_meta( $post->ID, 'session_cct_pulse', true );
		if ( empty( $data ) ) {
			$data = array();
		}
		
		$data = array_merge( array(
			'markers'     => "on",
			'mode'      => "enabled",
			'placeholder' => "",
			'num_char'    => 140,
		), $data );
		
		?>
		<!-- Module Mode -->
		<p>
			<label>
				Mode
				<select name="<?php $this->field_name( "meta", "mode" ); ?>">
					<option value="enabled" <?php selected( $data['meta']['mode'] == "enabled" ); ?>>Open</option>
					<option value="locked" <?php selected( $data['meta']['mode'] == "locked" ); ?>>Locked</option>
					<option value="disabled" <?php selected( $data['meta']['mode'] == "disabled" ); ?>>Disable Module</option>
				</select>
				<br />
				<small>When Locked, pulses will be displayed, but users will not be able to post any new ones.</small>
			</label>
		</p>
		<!-- Markers -->
		<p>
			<label>
				<input type="checkbox" name="<?php $this->field_name( "markers" ); ?>" <?php checked( $data['markers'] == "on" ); ?> />
				 Show Markers
				<br />
				<small>Show bookmark titles in the pulse list.</small>
			</label>
		</p>
		<!-- Placeholder -->
		<p>
			<label>
				Placeholder: <input class="widefat" name="<?php $this->field_name( "placeholder" ); ?>" type="text" value="<?php echo esc_attr( $data['placeholder'] ); ?>" />
			</label>
		</p>
		<!-- Character Count -->
		<p>
			<label>
				Limit Character Count:
				<br />
				<input name="<?php $this->field_name( "num_char" ); ?>" type="text" value="<?php echo esc_attr( $data['num_char'] ); ?>" />
				<br />
				<small class="clear">A counter restricting the number of characters a person can enter.</small>
			</label>
		</p>
		<?php
	}
	
	public function view() {
		$pulse = $this->data();
		
		wp_enqueue_script( 'popcornjs-pulse' );
		wp_enqueue_script( 'scct-view-pulse' );
		wp_enqueue_style( 'scct-view-pulse' );
		?>
		<div class="widget">
			<div id="scct-pulse-list" class="pulse-widget">
				<?php
					if ( $pulse['mode'] != 'locked' ) {
						Pulse_CPT_Form_Widget::pulse_form( $pulse );
					}
				?>
				<div id="pulse-list" class="pulse-list">
					<!-- To be populated by PopcornJS -->
				</div>
				<?php $it = Pulse_CPT::the_pulse_array_js( $pulse ); ?>
				<script id="pulse-cpt-single" type="text/x-dot-template">
					<?php Pulse_CPT::the_pulse( $it, false, true ); ?>
				</script>
			</div>
		</div>
		<?php
	}
	
	function localize_view( $data ) {
		$pulse_list = array();
		$args = Pulse_CPT_Form_Widget::query_arguments();
		$args['posts_per_page'] = -1;
		$args['orderby'] = "meta_value_num";
		$args['meta_key'] = "synctime";
		$args['order'] = "DESC";
		
		$pulse_query = new WP_Query( $args );
		while ( $pulse_query->have_posts() ) {
			$pulse_query->the_post();
			$pulse_list[] = Pulse_CPT::the_pulse_array();
		}
		
		// Reset Post Data
		wp_reset_postdata();
		
		$data['pulse'] = $pulse_list;
		return $data;
	}
	
	public function save( $post_id ) {
		$_POST['pulse'] = array_merge( array(
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
		
		parent::save( $post_id );
	}
	
	public static function modify_pulse( $pulse_id ) {
		if ( isset( $_POST['ss_synctime'] ) ) {
			update_post_meta( $pulse_id, "synctime", $_POST['ss_synctime'] );
		}
	}
	
	public function the_pulse_data( $data ) {
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

new SCCT_Module_Pulse();