<?php
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				printf( '<i class="icon-comments"></i> ' . _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'sp-theme' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'sp_comment', 'style' => 'ol' ) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation clearfix" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'sp-theme' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '<i class="icon-angle-left"></i> Older Comments', 'sp-theme' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <i class="icon-angle-right"></i>', 'sp-theme' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'sp-theme' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php
	$comment_form = array(
		'comment_notes_before' => '',
		'comment_notes_after' => '',
		'fields' => array(
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'sp-theme' ) . ' <span class="required">*</span></label>' .
			            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" aria-required="true" class="form-control" /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'sp-theme' ) . ' <span class="required">*</span></label>' .
			            '<input id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" aria-required="true" class="form-control" /></p>',
			'url' => '<p class="comment-form-url">' . '<label for="url">' . __( 'Website', 'sp-theme' ) . '</label>' .
			            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" class="form-control" /></p>'
		),
		'label_submit' => __( 'Post Comment', 'sp-theme' ),
		'comment_field' => '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'sp-theme' ) . '</label><textarea id="comment" name="comment" rows="8" aria-required="true" class="form-control"></textarea></p>'
	);

	comment_form( apply_filters( 'sp_post_comment_form_args', $comment_form ) );
	?>

</div><!-- #comments .comments-area -->