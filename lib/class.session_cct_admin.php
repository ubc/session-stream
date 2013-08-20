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
		
		ob_start();
		self::question_meta();
		wp_localize_script( 'scct-admin', 'scct_question_html', ob_get_clean() );
	}
	
	public static function enqueue_scripts_and_styles() {
		wp_enqueue_media();
		wp_enqueue_script( 'media-upload' );
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
		add_meta_box( 'session-cct-question', __( 'Questions', 'session-cpt' ), array( __CLASS__, 'question_meta_box' ), SESSION_CCT_SLUG, 'normal', 'default' );
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
				<input type="checkbox" name="bookmark_meta[show_time]" <?php checked( $bookmarks['show_time'] ); ?> />
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
		$type    = ( empty( $data['type']     ) ? "markup" : $data['type']    );
		$start   = ( empty( $data['start']    ) ? "0:00"   : $data['start']   );
		$end     = ( empty( $data['end']      ) ? "0:00"   : $data['end']     );
		$content = ( empty( $data['content']  ) ? ""       : $data['content'] );
		$image   = ( empty( $data['image']    ) ? ""       : $data['image']   );
		
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
				<select name="slides[][type]" class="scct-slide-type" value="<?php echo $type; ?>" onchange="Session_CCT_Admin.changeType( this );">
					<option value="markup" <?php selected( $type == "markup" ); ?>>Markup</option>
					<option value="image" <?php selected( $type == "image" ); ?>>Image</option>
				</select>
			</label>
			<label>
				Start At: 
				<input type="text" name="slides[][start]" value="<?php echo $start; ?>" />
			</label>
			<br />
			<span class="scct-slide-type-markup">
				<label>
					<textarea name="slides[][content]"><?php echo $content; ?></textarea>
				</label>
			</span>
			<span class="scct-slide-type-image">
				<label for="slides[][image]">
					<input class="upload-image-button button" type="button" value="Choose Image" />
					<input class="upload-image" type="text" size="36" name="slides[][image]" value="<?php echo $image; ?>" />
					<br />Enter an URL or choose an image for the slide.
				</label>
			</span>
		</div>
		<?php
	}
	
	public static function pulse_meta_box( $post, $box ) {
		$data = get_post_meta( $post->ID, 'session_cct_pulse', true );
		if ( empty( $data ) ) {
			$data = array();
		}
		
		$data = array_merge( array(
			'markers'     => "on",
			'status'      => "enabled",
			'placeholder' => "",
			'num_char'    => 140,
		), $data );
		
		?>
		<!-- Pulse Status -->
		<p>
			<label>
				Status:
				<br />
				<select name="pulse[status]">
					<option value="enabled" <?php selected( $data['status'] == "enabled" ); ?>>Open</option>
					<option value="locked" <?php selected( $data['status'] == "locked" ); ?>>Locked</option>
					<option value="disabled" <?php selected( $data['status'] == "disabled" ); ?>>Disabled</option>
				</select>
				<br />
				<small>When Locked, pulses will be displayed, but users will not be able to post any new ones.</small>
			</label>
		</p>
		<!-- Markers -->
		<p>
			<label>
				<input type="checkbox" name="pulse[markers]" <?php checked( $data['markers'] == "on" ); ?> />
				 Show Markers
				<br />
				<small>Show bookmark titles in the pulse list.</small>
			</label>
		</p>
		<!-- Placeholder -->
		<p>
			<label>
				Placeholder: <input class="widefat" name="pulse[placeholder]" type="text" value="<?php echo esc_attr( $data['placeholder'] ); ?>" />
			</label>
		</p>
		<!-- Character Count -->
		<p>
			<label>
				Limit Character Count:
				<br />
				<input name="pulse[num_char]" type="text" value="<?php echo esc_attr( $data['num_char'] ); ?>" />
				<br />
				<small class="clear">A counter restricting the number of characters a person can enter.</small>
			</label>
		</p>
		<?php
	}
	
	public static function question_meta_box( $post, $box ) {
		$questions = get_post_meta( $post->ID, 'session_cct_questions', true );
		
		?>
		<pre>
			<?php print_r( $questions ); ?>
		</pre>
		<div class="scct-admin-section">
			<label>
				Mode
				<select name="question_meta[mode]">
					<option value="skip" <?php selected( $questions['mode'] == 'skip' ); ?>>Skippable</option>
					<option value="any" <?php selected( $questions['mode'] == 'any' ); ?>>Must Answer</option>
					<option value="correct" <?php selected( $questions['mode'] == 'correct' ); ?>>Must Answer Correctly</option>
				</select>
			</label>
			<br />
			<label>
				<input type="checkbox" name="question_meta[random]" <?php checked( $questions['random'] ); ?> />
				Randomize Order
			</label>
		</div>
		<div class="scct-question-list">
			<?php
				foreach ( $questions['list'] as $question ) {
					self::question_meta( $question );
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addQuestion( this );">Add Question</a>
		<?php
	}
	
	public static function question_meta( $data = array() ) {
		$title   = ( empty( $data['title']   ) ? ""      : $data['title'] );
		$time    = ( empty( $data['time']    ) ? ""      : $data['time'] );
		$answers = ( empty( $data['answers'] ) ? array() : $data['answers'] );
		
		?>
		<div class="scct-question scct-admin-section">
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
				<input type="text" name="questions[][title]" value="<?php echo $title; ?>" />
			</label>
			<label>
				Time: 
				<input type="text" name="questions[][time]" value="<?php echo $time; ?>" />
			</label>
			<br />
			Answers
			<ul>
				<?php
					self::answer_meta( $answers[0] );
					self::answer_meta( $answers[1] );
					self::answer_meta( $answers[2] );
					self::answer_meta( $answers[3] );
				?>
			</ul>
		</div>
		<?php
	}
	
	public static function answer_meta( $data = array() ) {
		?>
		<li>
			<input type="text" name="questions[][answer_title]" value="<?php echo $data['title']; ?>" />
			<label>
				<input type="checkbox" name="questions[][answer_correct]" <?php checked( $data['correct'] == "on" ); ?> />
				This answer is correct.
			</label>
		</li>
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
	
	function starts_with( $haystack, $needle ) {
		return $needle === "" || strpos( $haystack, $needle ) === 0;
	}
	
}

Session_CCT_Admin::init();