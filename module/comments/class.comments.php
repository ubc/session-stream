<?php
class SCCT_Comments extends Session_CCT_Module {
	
	function __construct() {
		parent::__construct( array(
			'name'     => "Comments",
			'priority' => "high",
			'order'    => 13,
		) );
		
    	wp_register_style( 'scct-view-comments', SESSION_CCT_DIR_URL.'/module/comments/view-comments.css' );
    	
    	
	}
	
	public function load_admin() {
		add_filter( 'scct_localize_admin', array( $this, 'localize_admin' ) );
		
	}

	public function comments_template( $template ) {
		
		if( is_singular( SESSION_CCT_SLUG ) )
			return SESSION_CCT_DIR_PATH. "module/comments/template.php";
		return $template;

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
		<?php
				break;
			default :
			// Proceed with normal comments.
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment-item">
				<header class="comment-meta comment-author vcard">
					<?php
						

						if( 0 != $comment->comment_parent ) {
							echo get_avatar( $comment, 32 );
							printf( '<cite><b class="fn">%1$s</b></cite>', get_comment_author_link());
							printf( '<span class="reply-to"> <span class="genericon genericon-reply"></span>  <cite><b class="fn">%1$s</b></cite> </span>', get_comment_author_link( $comment->comment_parent ) ); 
						} else {
							printf( '<cite><b class="fn">%1$s</b></cite>', get_comment_author_link());
							echo get_avatar( $comment, 44 );
						}
						

						

						printf( '<span class="middle-dot"> &middot; </span> <a href="%1$s"><time datetime="%2$s">%3$s ago</time> </a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							human_time_diff( get_comment_time( 'U' ), current_time('timestamp') ) 
						);
					?>
					<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link comment-action">', '</p>' ); ?>
					<p class="reply comment-action">
					<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ),  'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</p><!-- .reply -->
				</header><!-- .comment-meta -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
				<?php endif; ?>

				<section class="comment-content comment">
					<?php comment_text(); ?>
					
				</section><!-- .comment-content -->

				
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // end comment_type check
    }
	
	public function load_style() {
		self::wp_enqueue_style( 'scct-view-comments' );
	}
	
	public function load_view() {
		add_filter( 'scct_localize_view', array( $this, 'localize_view' ) );
	}
	
	public function admin( $post, $box ) {
		
		?>
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
		// $this->admin_bookmark();
		$data['template']['bookmark'] = ob_get_clean();
		return $data;
	}
	
	public function view() {  
		
		comments_template( ); 
	}
	
	public function localize_view( $data ) {

		return $data;	
	}
	
	public function save( $post_id ) {
		
		parent::save( $post_id );
	}
}

new SCCT_Comments();