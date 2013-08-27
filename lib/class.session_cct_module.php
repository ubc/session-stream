<?php
class Session_CCT_Module {
	// ============================== STATIC ============================== //
	private static $modules;
	
	public static function init() {
		add_action( 'load-post.php',     array( __CLASS__, 'meta_box_setup' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'meta_box_setup' ) );
		add_action( 'save_post',         array( __CLASS__, 'save_post_meta' ), 10, 2 );
		add_action( 'scct_print_view',   array( __CLASS__, 'print_view' ) );
		add_action( 'wp',                array( __CLASS__, 'load_modules' ) );
	}
	
	static function meta_box_setup() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_box_add' ), 15 );
	}
	
	static function meta_box_add() {
		foreach ( self::$modules as $index => $module ) {
			add_meta_box( 'session-cct-'.$module->atts['slug'], __( $module->atts['name'], 'session-cct' ), array( $module, 'admin' ), SESSION_CCT_SLUG, $module->atts['context'], $module->atts['priority'] );
		}
	}
	
	static function load_modules() {
		if ( Session_CCT_View::is_active() ) {
			foreach ( self::$modules as $index => $module ) {
				if ( $module->atts['active'] ) {
					$module->load_view();
				}
			}
		}
	}
	
	static function print_view( $post_id ) {
		foreach ( self::$modules as $index => $module ) {
			$data = $module->data( $post_id );
			if ( $data['meta']['mode'] != 'disabled' ) {
				?>
				<div class="<?php echo $module->atts['slug']; ?>-wrapper scct-wrapper">
					<?php $module->view( $post_id ); ?>
				</div>
				<?php
			}
		}
	}
	
	static function save_post_meta( $post_id, $post_object ) {
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
		
		foreach ( self::$modules as $index => $module ) {
			$module->save( $post_id );
		}
	}
	
	
	// ============================== INSTANCE ============================== //
	protected $atts;
	
	function __construct( $atts ) {
		$this->atts = wp_parse_args( $atts, array(
			'name'     => "Untitled",
			'slug'     => null,
			'priority' => "default",
			'context'  => "normal",
			'active'   => true,
		) );
		
		if ( empty( $this->atts['slug'] ) ) {
			$this->atts['slug'] = self::slugify( $atts['name'] );
		}
		
		self::$modules[] = $this;
		add_action( 'admin_init', array( $this, 'load_admin' ) );
	}
	
	public function load_admin() {}
	public function load_view() {}
	
	public function admin() {
		?>
		<strong>Warning!</strong> This module has not implemented an admin view.
		<?php
	}
	
	public function view() {
		?>
		<strong>Warning!</strong> This module has not implemented a front end view.
		<?php
	}
	
	public function save( $post_id ) {
		update_post_meta( $post_id, 'session_cct_'.$this->atts['slug'], $_POST[$this->atts['slug']] );
	}
	
	public function data( $post_id = null ) {
		if ( $post_id == null ) {
			global $post;
			$post_id = $post->ID;
		}
		
		return get_post_meta( $post_id, 'session_cct_'.$this->atts['slug'], true );
	}
	
	public function field_name( $key ) {
		if ( is_array( $key ) ) {
			$key = implode( "][", $key );
		}
		
		echo $this->atts['slug']."[".$key."]";
	}
	
	
	// ============================== UTILITY ============================== //
	
	/** Courtesy of http://stackoverflow.com/a/2955878 */
	static public function slugify( $text ) {
		$original = $text;
		
		// replace non letter or digits by -
		$text = preg_replace( '~[^\\pL\d]+~u', '-', $text );
		// trim
		$text = trim( $text, '-' );
		// transliterate
		$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
		// lowercase
		$text = strtolower( $text );
		// remove unwanted characters
		$text = preg_replace( '~[^-\w]+~', '', $text );
		
		if ( empty( $text ) ) {
			error_log( "Session CCT: could not slugify ".$original );
			return null;
		}
		
		return $text;
	}
}

Session_CCT_Module::init();