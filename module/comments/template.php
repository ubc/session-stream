<?php 
	// this is a comments template
	wp_enqueue_script( 'comment-reply' );
?>
<div id="comments" class="comments-area white-popup mfp-hide">
	<?php
	global $current_user;
	// form args
	if( is_user_logged_in() ){
		$end = '</div><!-- end of comment-item -->';
	}
	$logged_in_as = '<div class="comment-item">'.get_avatar(  $current_user->user_email , 44 ).'<div class="logged-in-as" >' . sprintf( __( '<a href="%1$s" title="Logged in as %2$s">%2$s</a> <span class="middle-dot"> &middot; </span> <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</div>';
	$comment_field = '<div class="comment-form-comment"><label for="comment" class="screen-reader-text" >' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" placeholder="Join the conversation" rows="8" aria-required="true"></textarea></div>'.$end;
	
	$args = array( 
		
		'title_reply' => '',
		'label_submit' => 'Comment',
		'comment_notes_after'	=> '',
		'comment_field'	=> $comment_field,
		'logged_in_as'	=> 	$logged_in_as,
	 );
	 
	if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'twentytwelve' ),
					number_format_i18n( get_comments_number() ) );
			?>
		</h2>
		<?php comment_form( $args ); ?>

		<dl class="sub-nav"> <dt>Filter:</dt> <dd class="active"><a href="#">All</a></dd> <dd><a href="#">Active</a></dd> <dd><a href="#">Pending</a></dd> <dd><a href="#">Suspended</a></dd> </dl>


		<ol class="commentlist">
			<?php wp_list_comments( array(
				'callback'	 => array( 'SCCT_Comments', 'list_comment' ),
				'short_ping' => true,
				'avatar_size'=> 34,
			) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'twentytwelve' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentytwelve' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentytwelve' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'twentytwelve' ); ?></p>
		<?php endif; ?>
	<?php else:  ?>
	<?php comment_form( $args ); ?>
	<?php endif; // have_comments() ?>

	

</div><!-- #comments .comments-area -->
