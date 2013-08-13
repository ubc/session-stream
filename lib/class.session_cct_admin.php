<?php

class Session_CCT_Admin {
	
	public static function init() {
		add_action( 'admin_init',        array( __CLASS__, 'load' ) );
		
		self::register_scripts_and_styles();
	}
	
	public static function load() {
		add_action( 'load-post.php',     array( __CLASS__, 'meta_box_setup' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'meta_box_setup' ) );
		add_action( 'save_post',         array( __CLASS__, 'save_post_meta' ), 10, 2 );
		
		self::enqueue_scripts_and_styles();
	}
	
	public static function register_scripts_and_styles() {
    	wp_register_script( 'scct-admin', SESSION_CCT_DIR_URL.'/js/admin.js', array( 'popcornjs', 'jquery' ), '1.0', true );
    	wp_register_style(  'scct-admin', SESSION_CCT_DIR_URL.'/css/admin.css' );
		
		ob_start();
		self::slide_meta();
		wp_localize_script( 'scct-admin', 'scct_slide_html', ob_get_clean() );
		
		ob_start();
		self::bookmark_meta();
		wp_localize_script( 'scct-admin', 'scct_bookmark_html', ob_get_clean() );
	}
	
	public static function enqueue_scripts_and_styles() {
    	wp_enqueue_script( 'scct-admin' );
    	wp_enqueue_style( 'scct-admin' );
	}
	
	public static function meta_box_setup() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box_remove' ), 15 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box_add' ), 15 );
	}
	
	public static function meta_box_add() {
		add_meta_box( 'session-cct-media',    __( 'Media',     'session-cct' ), array( __CLASS__, 'media_meta_box'    ), SESSION_CCT_SLUG, 'normal', 'high' );
		add_meta_box( 'session-cct-bookmark', __( 'Bookmarks', 'session-cct' ), array( __CLASS__, 'bookmark_meta_box' ), SESSION_CCT_SLUG, 'normal', 'high' );
		add_meta_box( 'session-cct-slide',    __( 'Slides',    'session-cct' ), array( __CLASS__, 'slide_meta_box'    ), SESSION_CCT_SLUG, 'normal', 'high' );
		add_meta_box( 'session-cct-pulse',    __( 'Pulse CPT',   'pulse-cpt' ), array( __CLASS__, 'pulse_meta_box'    ), SESSION_CCT_SLUG, 'normal', 'default' );
	}
	
	public static function meta_box_remove() {
		remove_meta_box( 'pulse-post-meta', SESSION_CCT_SLUG, 'side' );
	}
	
	public static function media_meta_box( $post, $box ) {
		$media = get_post_meta( $post->ID, 'session_cct_media', true );
		$type = ( empty( $media['type'] ) ? "youtube" : $media['type'] );
		$url  = ( empty( $media['url']  ) ? ""        : $media['url']  );
		
		?>
		<label for="media[type]">
			<select name="media[type]">
				<option value="youtube" <?php selected( $type == "youtube" ); ?>>YouTube</option>
				<option value="vimeo" <?php selected( $type == "vimeo" ); ?>>Vimeo</option>
				<option value="soundcloud" <?php selected( $type == "soundcloud" ); ?>>SoundCloud</option>
			</select>
		</label>
		<label for="media[url]">
			<input type="url" name="media[url]" value="<?php echo $url; ?>" />
		</label>
		<?php
	}
	
	public static function bookmark_meta_box( $post, $box ) {
		$bookmarks = get_post_meta( $post->ID, 'session_cct_bookmarks', true );
		
		?>
		<div class="scct-admin-section">
			<label>
				<input type="checkbox" name="bookmark_meta[show_time]" <?php checked( true ); ?> />
				Show Timestamp
			</label>
		</div>
		<div class="scct-bookmark-list">
			<?php
				foreach ( $bookmarks['list'] as $bookmark ) {
					self::bookmark_meta( $bookmark );
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addBookmark( this );">Add Bookmark</a>
		<?php
	}
	
	public static function bookmark_meta( $data = array() ) {
		$title = ( empty( $data['title'] ) ? "" : $data['title'] );
		$time  = ( empty( $data['time']  ) ? "" : $data['time']  );
		
		?>
		<div class="scct-bookmark scct-admin-section">
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
			<label>
				Title: 
				<input type="text" name="bookmarks[][title]" value="<?php echo $title; ?>" />
			</label>
			<label>
				Time: 
				<input type="text" name="bookmarks[][time]" value="<?php echo $time; ?>" />
			</label>
		</div>
		<?php
	}
	
	public static function slide_meta_box( $post, $box ) {
		$slides = get_post_meta( $post->ID, 'session_cct_slides', true );
		$offset = ( empty( $slides['offset'] ) ? 0 : $slides['offset'] );
		
		?>
		<div class="scct-admin-section">
			<label>
				Start the first slide at: 
				<input type="number" name="slide_meta[offset]" value="<?php echo $offset; ?>" />
				seconds.
			</label>
		</div>
		<div class="scct-slide-list">
			<?php
				foreach ( $slides['list'] as $slide ) {
					self::slide_meta( $slide );
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addSlide( this );">Add Slide</a>
		<?php
	}
	
	public static function slide_meta( $data = array() ) {
		$type     = ( empty( $data['type']     ) ? "markup" : $data['type']     );
		$duration = ( empty( $data['duration'] ) ? 0        : $data['duration'] );
		$content  = ( empty( $data['content']  ) ? ""       : $data['content']  );
		$image    = ( empty( $data['image']    ) ? ""       : $data['image']    );
		
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
			<label>
				Type: 
				<select name="slides[][type]" value="<?php echo $type; ?>" onchange="Session_CCT_Admin.changeType( this );">
					<option value="markup" <?php selected( $type == "markup" ); ?>>Markup</option>
					<option value="image" <?php selected( $type == "image" ); ?>>Image</option>
				</select>
			</label>
			<label>
				Duration: 
				<input type="number" name="slides[][duration]" value="<?php echo $duration; ?>" />
				seconds.
			</label>
			<br />
			<span class="scct-slide-type-markup">
				<label>
					<textarea name="slides[][content]"><?php echo $content; ?></textarea>
				</label>
			</span>
			<span class="scct-slide-type-image">
				<label>
					Image
					<input type="url" name="slides[][image]" value="<?php echo $image; ?>" />
				</label>
			</span>
		</div>
		<?php
	}
	
	public static function pulse_meta_box( $post, $box ) {
		Pulse_CPT_Admin::pulse_meta_box( $post, $box );
		?>
		<br /><br />
		TODO: Add some custom configuration for pulse, for this Session.
		<?php
	}
	
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
		
		update_post_meta( $post_id, 'session_cct_slides', $slides );
		update_post_meta( $post_id, 'session_cct_bookmarks', $bookmarks );
		update_post_meta( $post_id, 'session_cct_media', $_POST['media'] );
		
		return true;
	}
	
}

Session_CCT_Admin::init();