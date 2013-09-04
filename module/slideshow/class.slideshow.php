<?php
class SCCT_Module_Slideshow extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Slides",
			'slug'     => "slideshow",
			'priority' => "high",
			'order'    => 5,
		) );
		
    	wp_register_style(  'scct-view-slideshow', SESSION_CCT_DIR_URL.'/module/slideshow/view-slideshow.css' );
    	wp_register_script( 'scct-view-slideshow', SESSION_CCT_DIR_URL.'/module/slideshow/view-slideshow.js',  array( 'jquery' ), '1.0', true );
    	wp_register_script( 'scct-admin-slideshow', SESSION_CCT_DIR_URL.'/module/slideshow/admin-slideshow.js',  array( 'jquery' ), '1.0', true );
	}
	
	public function load_admin() {
		add_filter( 'scct_localize_admin', array( $this, 'localize_admin' ) );
		
		wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'scct-admin-slideshow' );
	}
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
		
		wp_enqueue_style(  'scct-view-slideshow' );
		wp_enqueue_script( 'scct-view-slideshow' );
	}
	
	public function admin( $post, $box ) {
		$slides = $this->data( $post->ID );
		
		?>
		<div class="scct-admin-section">
			<!-- Module Mode -->
			<label>
				Mode
				<select name="<?php $this->field_name( "meta", "mode" ); ?>">
					<option value="enabled" <?php selected( $data['meta']['mode'] == "enabled" ); ?>>Enabled</option>
					<option value="disabled" <?php selected( $data['meta']['mode'] == "disabled" ); ?>>Disable Module</option>
				</select>
			</label>
		</div>
		<div class="scct-slide-list scct-section-list">
			<?php
				if ( ! empty( $slides['list'] ) ) {
					foreach ( $slides['list'] as $slide ) {
						$this->admin_slide( $slide );
					}
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addSection( this, 'slide' );">Add Slide</a>
		<?php
	}
	
	public function admin_slide( $data = array() ) {
		$type    = ( empty( $data['type']    ) ? "markup" : $data['type']    );
		$start   = ( empty( $data['start']   ) ? "0:00"   : $data['start']   );
		$end     = ( empty( $data['end']     ) ? "0:00"   : $data['end']     );
		$content = ( empty( $data['content'] ) ? ""       : $data['content'] );
		$image   = ( empty( $data['image']   ) ? ""       : $data['image']   );
		
		?>
		<div class="scct-slide show-markup scct-admin-section">
			<span class="scct-section-meta">
				<a class="scct-close" onclick="Session_CCT_Admin.removeSection( this );">
					&#10006;
				</a>
				<a class="scct-up" onclick="Session_CCT_Admin.move( this, false );">
					<img src="<?php echo SESSION_CCT_DIR_URL; ?>/img/arrow-down.png" />
				</a>
				<a class="scct-down" onclick="Session_CCT_Admin.move( this, true );">
					<img src="<?php echo SESSION_CCT_DIR_URL; ?>/img/arrow-up.png" />
				</a>
			</span>
			<label for="<?php $this->field_name( array( "list", "", "type" ) ); ?>">
				Type: 
				<select name="<?php $this->field_name( array( "list", "", "type" ) ); ?>" class="scct-slide-type" value="<?php echo $type; ?>">
					<option value="markup" <?php selected( $type == "markup" ); ?>>Markup</option>
					<option value="image" <?php selected( $type == "image" ); ?>>Image</option>
				</select>
			</label>
			<label for="<?php $this->field_name( array( "list", "", "start" ) ); ?>">
				Start At: 
				<input type="text" name="<?php $this->field_name( array( "list", "", "start" ) ); ?>" value="<?php echo $start; ?>" />
			</label>
			<br />
			<span class="scct-slide-type-markup">
				<label>
					<textarea name="<?php $this->field_name( array( "list", "", "content" ) ); ?>"><?php echo $content; ?></textarea>
				</label>
			</span>
			<span class="scct-slide-type-image">
				<label for="<?php $this->field_name( array( "list", "", "image" ) ); ?>">
					<input class="upload-image-button button" type="button" value="Choose Image" />
					<input class="upload-image" type="text" size="36" name="<?php $this->field_name( array( "list", "", "image" ) ); ?>" value="<?php echo $image; ?>" />
					<br />Enter an URL or choose an image for the slide.
				</label>
			</span>
		</div>
		<?php
	}
	
	function localize_admin( $data ) {
		ob_start();
		$this->admin_slide();
		$data['template']['slide'] = ob_get_clean();
		return $data;
	}
	
	public function view() {
		?>
		<div id="slide-list" class="slide-list"></div>
		<?php
	}
	
	function localize_view( $data ) {
		$slideshow = $this->data();
		
		if ( ! empty( $slideshow['list'] ) ) {
			foreach ( $slideshow['list'] as $index => $slide ) {
				error_log('test');
				if ( ! empty( $slide['content'] ) ) {
					error_log("do shortcode on ".$slide['content']);
					$slide['content'] = do_shortcode( $slide['content'] );
				} else {
					error_log("empty ".$slide['content']);
				}
				
				$slide['start'] = Session_CCT_View::string_to_seconds( $slide['start'] );
				$slideshow['list'][$index] = $slide;
			}
		}
		
		$data['slideshow'] = $slideshow;
		return $data;
	}
	
	public function save( $post_id ) {
		$slide = null;
		$list = array();
		foreach ( $_POST[$this->atts['slug']]['list'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( $key == 'type' ) {
				if ( ! empty( $slide ) ) {
					$list[] = $slide;
				}
				
				$slide = array();
			}
			
			$slide[$key] = $value;
		}
		$list[] = $slide;
		usort( $list, array( $this, 'compare_slides' ) );
		
		$_POST[$this->atts['slug']]['list'] = $list;
		
		parent::save( $post_id );
	}
	
	public function compare_slides( $a, $b ) {
		return Session_CCT_View::string_to_seconds( $a['start'] ) > Session_CCT_View::string_to_seconds( $b['start'] );
	}
}

new SCCT_Module_Slideshow();