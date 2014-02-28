<?php


class SCCT_Media extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Media",
			'priority' => "high",
			'order'    => 1,
		) );
		
    	wp_register_style(  'scct-view-media', SESSION_CCT_DIR_URL.'/module/media/view-media.css' );
    	wp_register_script( 'scct-view-media', SESSION_CCT_DIR_URL.'/module/media/view-media.js',  array( 'jquery' ), '1.0', true );
	}
	
	public function load_view() {

		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}

	public function load_style(){

		self::wp_enqueue_style( 'scct-view-media' );

	}
	
	public function admin( $post, $box ) {
		$media = $this->data( $post->ID );
		?>
		<label for="<?php $this->field_name( "type" ); ?>">
			<select name="<?php $this->field_name( "type" ); ?>">
				<option value="youtube" <?php selected( $media['type'] == "youtube" ); ?>>YouTube</option>
				<option value="vimeo" <?php selected( $media['type'] == "vimeo" ); ?>>Vimeo</option>
				<option value="soundcloud" <?php selected( $media['type'] == "soundcloud" ); ?>>SoundCloud</option>
			</select>
		</label>
		<label for="<?php $this->field_name( "url" ); ?>">
			<input type="url" name="<?php $this->field_name( "url" ); ?>" value="<?php echo $media['url']; ?>" />
		</label>
		<?php
	}
	
	public function view() {
		$media = $this->data();
		wp_enqueue_script( 'scct-view-media'); ?>
		<div id="scct-media" class="iframe-wrapper <?php echo $media['type']; ?>"></div>
		<?php
	}
	
	public function localize_view( $data ) {
		$data['media'] = $this->data();
		return $data;
	}
}

$scct_media = new SCCT_Media();