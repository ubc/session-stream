<?php
class SCCT_Module_Bookmarks extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Bookmarks",
			'priority' => "high",
			'order'    => 8,
		) );
		
    	wp_register_style( 'scct-view-bookmarks', SESSION_CCT_DIR_URL.'/module/bookmarks/view-bookmarks.css' );
	}
	
	public function load_admin() {
		add_filter( 'scct_localize_admin', array( $this, 'localize_admin' ) );
	}
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}
	
	public function admin( $post, $box ) {
		$bookmarks = get_post_meta( $post->ID, 'session_cct_bookmarks', true );
		
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
			<br />
			<!-- Show Timestamp -->
			<label>
				<input type="checkbox" name="<?php $this->field_name( array( "meta", "show_time" ) ); ?>" <?php checked( $bookmarks['show_time']['meta'] ); ?> />
				Show Timestamp
			</label>
		</div>
		<div class="scct-bookmark-list scct-section-list">
			<?php
				if ( ! empty( $bookmarks['list'] ) ) {
					foreach ( $bookmarks['list'] as $bookmark ) {
						$this->admin_bookmark( $bookmark );
					}
				}
			?>
		</div>
		<a class="button" onclick="Session_CCT_Admin.addSection( this, 'bookmark' );">Add Bookmark</a>
		<?php
	}
	
	public function admin_bookmark( $data = array() ) {
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
				<input type="text" name="<?php $this->field_name( array( "list", "", "title" ) ); ?>" value="<?php echo $title; ?>" />
			</label>
			<label>
				Time: 
				<input type="text" name="<?php $this->field_name( array( "list", "", "time" ) ); ?>" value="<?php echo $time; ?>" />
			</label>
		</div>
		<?php
	}
	
	function localize_admin( $data ) {
		ob_start();
		$this->admin_bookmark();
		$data['template']['bookmark'] = ob_get_clean();
		return $data;
	}
	
	public function view() {
		$bookmarks = $this->data();
		
		wp_enqueue_style( 'scct-view-bookmarks' );
		?>
		<ul id="scct-bookmarks">
			<li class="title">
				Bookmarks
			</li>
			<?php
				foreach ( $bookmarks['list'] as $bookmark ) {
					$this->view_bookmark( $bookmark );
				}
			?>
		</ul>
		<?php
	}
	
	public function view_bookmark( $data = array() ) {
		$title = ( empty( $data['title'] ) ? ""     : $data['title'] );
		$time  = ( empty( $data['time']  ) ? "0:00" : $data['time']  );
		
		$seconds = Session_CCT_View::string_to_seconds( $time );
		$action = "SCCT_Module_Media.skipTo(".$seconds.");";
		
		?>
		<li class="control" title="<?php echo $title; ?>">
			<a class="scct-bookmark" onclick="<?php echo $action; ?>">
				<?php echo $title; ?><span class="timestamp"><?php echo $time; ?></span>
			</a>
		</li>
		<?php
	}
	
	function localize_view( $data ) {
		$data['bookmarks'] = $this->data();
		
		foreach ( $data['bookmarks']['list'] as $index => $bookmark ) {
			$data['bookmarks']['list'][$index]['synctime'] = Session_CCT_View::string_to_seconds( $bookmark['time'] );
		}
		
		return $data;
	}
	
	public function save( $post_id ) {
		$bookmark = null;
		$list = array();
		foreach ( $_POST[$this->atts['slug']]['list'] as $field ) {
			reset( $field );
			$key = key( $field );
			$value = $field[$key];
			
			if ( $key == 'title' ) {
				if ( ! empty( $bookmark ) ) {
					$list[] = $bookmark;
				}
				
				$bookmark = array();
			}
			
			$bookmark[$key] = $value;
		}
		$list[] = $bookmark;
		$_POST[$this->atts['slug']]['list'] = $list;
		
		parent::save( $post_id );
	}
}

new SCCT_Module_Bookmarks();