<?php


/**
 * 
 */
class SCCT_Comments extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Comments",
			'priority' => "high",
			'order'    => 13,
			'has_view' => false,
			'has_view_sidebar' => true
		) );
		
    	wp_register_style( 'scct-view-comments', SESSION_CCT_DIR_URL.'/module/comments/view-comments.css' );
    	wp_register_script( 'scct-view-coments', SESSION_CCT_DIR_URL.'/module/comments/view-comments.js', array( 'jquery', 'backbone', 'scct-view' ), '1.0', true );

    	add_filter( 'comments_template', array( $this, 'comments_template' ) );
    	
    	add_action('wp_ajax_nopriv_session-stream-comments', array( $this, 'handel_ajax' ) );
    	add_action('wp_ajax_session-stream-comments', array( $this, 'handel_ajax' ) );

    	add_action( 'wp_footer', array( $this, 'js_templates' ) );

	}
	
	public function load_admin() {
		add_filter( 'scct_localize_admin', array( $this, 'localize_admin' ) );
	}
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}

	public function load_style() {
		
		self::wp_enqueue_style( 'scct-view-comments' );
	}

	public function comments_template( $template ) {
		
		if( is_singular( SESSION_CCT_SLUG ) )
			return SESSION_CCT_DIR_PATH. "module/comments/template.php";
		return $template;

	}

	
	/**
	 * [admin description]
	 * @param  [type] $post [description]
	 * @param  [type] $box  [description]
	 * @return [type]       [description]
	 */
	public function admin( $post, $box ) { ?>
		<div class="scct-admin-section">
		<label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked($post->comment_status, 'open'); ?> /> <?php _e( 'Enable comments.' ) ?></label><br />
		</div>
		<div id="commentsdiv">
			<?php post_comment_meta_box( $post ); ?>
		</div>
		<?php
	}
	
	
	
	/**
	 * [localize_admin description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function localize_admin( $data ) {
		ob_start();
		
		return $data;
	}
	/**
	 * Display the comments when they are open
	 * 
	 * @return null
	 */
	public function view() {  
		if( comments_open() ) {
			wp_enqueue_script( 'scct-view-coments' );
			comments_template(); 
		}
		
	}
	/**
	 * Comment Template 
	 * 
	 * @param  object $comment 
	 * @return string HTML of the comment
	 */
	function comment_template( $comment ) { 

		ob_start();
		
		?>
		<li id="li-comment-<?php echo $comment['ID']; ?>">
			<article id="comment-<?php echo $comment['ID']; ?>" class="comment-item">
				<header class="comment-meta comment-author">
					<?php
						
						if( 0 != $comment['parent']['ID'] ) {
							echo $comment['author']['avatar'];
							printf( '<cite><b class="fn">%1$s</b></cite>', $comment['author']['link']);
							printf( '<span class="reply-to"> <span class="genericon genericon-reply"></span>  <cite><b class="fn">%1$s</b></cite> </span>', $comment['parent']['author_link'] ); 
						} else {
							printf( '<cite><b class="fn">%1$s</b></cite>', $comment['author']['link']);
							echo $comment['author']['avatar'];
						}
						
						printf( '<span class="middle-dot"> &middot; </span> <a href="%1$s"><time datetime="%2$s">%3$s ago</time> </a>',
							$comment['url'],
							$comment['data'],
							/* translators: 1: date, 2: time */
							$comment['human_date']
						);
					?>
					<?php 
					echo $comment['action']['edit']; 
					echo $comment['action']['reply']; 
					?>
				</header><!-- .comment-meta -->

				<?php if ( '0' == $comment['status'] ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
				<?php endif; ?>

				<section class="comment-content comment">
					<?php echo  $comment['text']; ?>
				</section><!-- .comment-content -->
			</article><!-- #comment-## -->
		</li>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	static function list_comment( $comment, $args, $depth ) {

		$GLOBALS['comment'] = $comment;
		
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
				// Display trackbacks differently than normal comments.
				?>
				<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
				</li>
				<?php
				break;
			default :
				// Proceed with normal comments.
				global $post;

				$comment_data = self::get_comment( $comment );
				echo self::comment_template( $comment_data );

			break;
		endswitch; // end comment_type check
    }
	
	public function localize_view( $data ) {
		global $post;
		$args = array(
			'post_id' => $post->ID
			);

		$data['comments'] = $this->get_comments( $args );
		return $data;	
	}

	function get_comment( $post_comment ) {

		global $comment;
		$preseve_comment = $comment;
		$comment = $post_comment;

		$comment_data = array(
			'ID' 			=> $comment->comment_ID,
			'text' 	=> $comment->comment_content,
			'url' 			=> get_comment_link( $comment->comment_ID ),
			
			'status'=> $comment->comment_approved,
			'date' 	=> $comment->comment_date,
			'human_date' => human_time_diff( get_comment_time( 'U' ), current_time('timestamp') ),
			'parent' 		=> array( 
				'ID' 		=> $comment->comment_parent,
				'author_link'=> get_comment_author_link( $comment->comment_parent )
				),
			'author'=> array(
				'ID' 		=> $comment->user_id,
				'name' 		=> $comment->comment_author,
				'email' 	=> $comment->comment_author_email,
				'link'		=> get_comment_author_link( $comment->comment_ID ),
				'avatar'	=> get_avatar( $comment->comment_author_email, 32 )
				),
			'action'=> array(
				'edit' 		=> '<p class="edit-link comment-action"> <a href="' . get_edit_comment_link( $comment->comment_ID ) . '"><span class="genericon genericon-edit"></span> <span class="screen-reader-text">'.__( 'Edit', 'twentytwelve' ). '</span></a></p>',
				'reply' 	=> '<p class="reply comment-action"> <a href="#" ><span class="genericon genericon-reply-alt"></span><span class="screen-reader-text">'.__( 'Reply' ). '</span></a></p>'
				)
		);
		
		$comment = $preseve_comment;
		unset( $preseve_comment );

		return $comment_data;


	}

	public function get_comments( $args ) {
		
		$comments = get_comments( $args );
		$comments_json = array();

		foreach($comments as $comment)
			$comments_json[] = $this->get_comment( $comment );

		return $comments_json;
	}

	function get_single_comment( $comment_id ) {
		return $this->get_comment( get_comment( $comment_id ) );

	}


	
	public function save( $post_id ) {
		
		parent::save( $post_id );
	}

	function hande_comment(  $commentdata ) {
		
		$comment_post_ID = isset( $commentdata['comment_post_ID']) ? (int) $commentdata['comment_post_ID'] : 0;

		$comment_author       = ( isset( $commentdata['author']) )  ? trim(strip_tags( $commentdata['author'])) : null;
		$comment_author_email = ( isset( $commentdata['email']) )   ? trim( $commentdata['email']) : null;
		$comment_author_url   = ( isset( $commentdata['url']) )     ? trim( $commentdata['url']) : null;
		$comment_content      = ( isset( $commentdata['comment']) ) ? trim( $commentdata['comment']) : null;

		// If the user is logged in
		$user = wp_get_current_user();
		if ( $user->exists() ) {
			if ( empty( $user->display_name ) )
				$user->display_name=$user->user_login;
			$comment_author       = wp_slash( $user->display_name );
			$comment_author_email = wp_slash( $user->user_email );
			$comment_author_url   = wp_slash( $user->user_url );

		} else {
			if ( get_option('comment_registration') )
				return array( 'error' =>  __('Sorry, you must be logged in to post a comment.') );
		}

		$comment_type = '';

		if ( get_option('require_name_email') && !$user->exists() ) {
			if ( 6 > strlen($comment_author_email) || '' == $comment_author )
				return array( 'error' =>  __('<strong>ERROR</strong>: please fill the required fields (name, email).') ) ;
			elseif ( !is_email($comment_author_email))
				return array( 'error' =>  __('<strong>ERROR</strong>: please enter a valid email address.') ) ;
		}

		if ( '' == $comment_content )
			return array( 'error' => __('<strong>ERROR</strong>: please type a comment.' ) );

		$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

		return compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID' );



	}
	
	public function handel_ajax() {
		
		switch( $_REQUEST['make'] ){

			case 'new':
				// comment_post_ID
				// comment_parent
				// comment
				// 
				
				// we need to check this...
				
				$commentdata = $this->hande_comment( wp_parse_args( $_POST['form'] ) );
				//var_dump( $commentdata );
				
				$comment_ID  = wp_new_comment( $commentdata );
				$return_data['comment'] = $this->get_single_comment( $comment_ID );
			break;
			
			case 'update': // same as edit
				// wp_update_comment( $commentdata );
			break;

			case 'trash': // delete the comment
				// wp_delete_comment($v);
			break;

			case 'get': // check if we have any new comments
				$args = array();
				$return_data['comments'] = $this->get_comments( $args );
			break;
		}

		$return_data['comment_count'] = get_comments_number();
		// do your ajax stuff here! 
		echo json_encode( $return_data );
		die();


	}

	public function js_templates( ) {
		
		$comment_data = array(
			'ID' 			=> '<%= ID %>',
			'text' 			=> '<%= text %>',
			'url' 			=> '<%= url %>',
			
			'status'		=> '<%= status %>',
			'date' 			=> '<%= date %>',
			'human_date' 	=> '<%= human_date %>',
			'parent' => array( 
				'ID' 		=> '<%= parent.ID %>',
				'author_link'=> '<%= parent.author_link %>'
				),
			'author' => array(
				'ID' 		=> '<%= author.ID %>',
				'name' 		=> '<%= author.name %>',
				'email' 	=> '<%= author.email %>',
				'link'		=> '<%= author.link %>',
				'avatar'	=> '<%= author.avatar %>'
				),
			'action'=> array(
				'edit' 		=> '<%= action.edit %>',
				'reply' 	=> '<%= action.reply %>'
				)
		);
		?>
		<script type="text/template" id="comment-template">
		<?php echo $this->comment_template( $comment_data ); ?>
		</script>
		<?php
	}


}

new SCCT_Comments();
