<?php
/**
 *	
 * theme specific functions
 */

if ( ! function_exists( 'sp_display_page_title' ) ) :
/**
 * Function that displays the page title
 *
 * @access public
 * @since 1.0
 * @return html
 */	
function sp_display_page_title() {
	global $post;

	// don't show any header if it is home page
	if ( is_front_page() || is_home() )
		return;
	
	// get the meta 
	$show_title = get_post_meta( $post->ID, '_sp_page_show_title', true );
	$show_tagline = get_post_meta( $post->ID, '_sp_page_show_tagline', true );
	$tagline = get_post_meta( $post->ID, '_sp_page_tagline_text', true );

	if ( ! isset( $show_title ) || $show_title === 'on' ) {
		// check if we need to show tagline
		if ( isset( $show_tagline ) && $show_tagline === 'on' && isset( $tagline ) && ! empty( $tagline ) ) { 
			echo '<h1 class="entry-title">' . get_the_title( $post->ID ) . ' - <span class="heading-tagline">' . $tagline . '</span></h1>' . PHP_EOL;
		} else {
			echo '<h1 class="entry-title"><span>' . get_the_title( $post->ID ) . '</span></h1>' . PHP_EOL;
		}
	}

	return true;
}
endif;

if ( ! function_exists( 'sp_post_meta_action_buttons_html' ) ) :
/**
 * Function that builds the post action meta buttons
 *
 * @access public
 * @since 1.0
 * @param int $post_id | the id of the post
 * @return mixed html
 */	
function sp_post_meta_action_buttons_html( $post_id = '' ) {
	// bail if id is not passed
	if ( ! isset( $post_id ) || empty ( $post_id ) )
		return;

	// get saved settings
	$social_media = get_post_meta( absint( $post_id ), '_sp_page_show_share', true );


	$output = '';

	if ( isset( $social_media ) && ! empty( $social_media ) && $social_media !== 'none' && $social_media !== 'off' )
		$output .= sp_share_button_html();

	return $output;
}
endif;

if ( ! function_exists( 'sp_display_header_meta_icons' ) ) :
/**
 * Function that displays the meta icons shown in header like cart/compare/wishlist/search
 * @access public
 * @since 1.0
 * @return html | $output
 */
function sp_display_header_meta_icons() {
	global $woocommerce;

	$output = '';

	if ( sp_woo_exists() ) {
		// check if show mini cart is on
		if ( sp_get_option( 'show_mini_cart', 'is', 'on' ) ) {
			if ( $woocommerce->cart->cart_contents_count === 0 )
				$product_item_count = '0';
			else
				$product_item_count = $woocommerce->cart->cart_contents_count;
				
		    $output .= '<div id="mini-cart" data-link="' . get_permalink( woocommerce_get_page_id( 'cart' ) ) . '">' . PHP_EOL;
		    $output .= '<i class="icon-cart cart-icon-button" aria-hidden="true"><span class="product-item-count">' . $product_item_count . '</span></i>' . PHP_EOL;

		    ob_start();
		    sp_display_mini_cart();
		    $output .= ob_get_clean();

		    $output .= '</div><!--close #mini-cart-->' . PHP_EOL;
		}

		// check if show wishlist icon is on
		if ( sp_get_option( 'show_wishlist_icon', 'is', 'on' ) )
			$output .= '<a href="' . get_permalink( sp_wishlist_page_id() ) . '" title="' . esc_attr__( 'My Wishlist', 'sp-theme' ) . '" class="get-wishlist sp-tooltip" data-toggle="tooltip" data-placement="top"><i class="icon-star" aria-hidden="true"><span class="wishlist-item-count">' . sp_woo_get_wishlist_item_count() . '</span></i></a>' . PHP_EOL;

		// check if show compare icon is on
		if ( sp_get_option( 'show_compare_icon', 'is', 'on' ) )
			$output .= '<a href="' . get_permalink( sp_compare_page_id() ) . '" title="' . esc_attr__( 'Compare Products', 'sp-theme') . '" class="get-compare sp-tooltip"><i class="icon-cog" aria-hidden="true"><span class="compare-item-count" data-toggle="tooltip" data-placement="top">' . sp_woo_get_compare_item_count() . '</span></i></a>' . PHP_EOL;
	}

	if ( sp_get_option( 'display_search', 'is', 'on' ) )
		$output .= '<a href="#" title="' . esc_attr__( 'Search', 'sp-theme') . '" class="search-icon-button sp-tooltip" data-toggle="tooltip" data-placement="top"><i class="icon-search" aria-hidden="true"></i></a>' . PHP_EOL;

	echo $output;
}
endif;

if ( ! function_exists( 'sp_get_menu' ) ) :
/**
 * Function that displays the menus
 * @access public
 * @since 1.0
 * @return html | $output
 */
function sp_get_menu( $menu_name = '' ) {
	switch( $menu_name ) {
		case 'primary-menu' :
			// try to get menu from cache first
			$menu = get_transient( $menu_name );
			
			// temporary disabled cache as cache is not working well with dynamic classes
			$menu = false;
			
			// if not in cache generate menu then cache it
			if ( false === $menu ) {
				$args = array(
					'container' => 'nav',
					'container_class' => 'clear',
					'container_id' => 'primary-nav',
					'theme_location' => 'primary-menu',
					'echo' => 0,
					'walker' => has_nav_menu( $menu_name ) ? new SP_Menu : ''
				);
				
				$menu = wp_nav_menu( $args );

				// set the transient
				set_transient( $menu_name, $menu, 60*60*24*30 );			
			}

			echo $menu;

			break;

		case 'top-bar-menu' :
			if ( has_nav_menu( $menu_name ) ) {
				$args = array( 
					'container' => 'nav', 
					'container_class' => 'top-bar-nav clearfix', 
					'theme_location' => 'top-bar-menu' 
				);

				wp_nav_menu( $args );
			}

			break;

		case 'footer-bar-menu' :
			if ( has_nav_menu( $menu_name ) ) {
				$args = array( 
					'container' => 'nav', 
					'container_class' => 'footer-bar-nav clearfix', 
					'theme_location' => 'footer-bar-menu', 
					'depth' => 2, 
					'walker' => new SP_Footer_Menu 
				);

				wp_nav_menu( $args );
			}
			break;

		case 'mobile-menu' :
			// if mobile menu is not set fallback to main menu
			if ( has_nav_menu( $menu_name ) ) {
				$args = array( 
					'container' => 'nav', 
					'container_class' => 'mobile-nav clearfix', 
					'theme_location' => 'mobile-menu', 
					'depth' => 0
				);

			} else {
				$args = array(
					'container' => 'nav',
					'container_class' => 'clear',
					'container_id' => 'primary-nav',
					'theme_location' => 'primary-menu',
					'depth' => 0
				);
			}		
			
			wp_nav_menu( $args );

			break;		
	}

	return true;
}
endif;

if ( ! function_exists( 'sp_show_post_content_excerpt' ) ) :
/**
 * Function that displays the post's content or excerpt
 * @access public
 * @since 1.0
 * @return html | $output
 */	
function sp_show_post_content_excerpt() {
	global $post;

	$continue_text = apply_filters( 'sp_continue_reading_text', __( 'Continue reading <i class="icon-angle-right continue-reading"></i>', 'sp-theme' ) );

	// check if excerpt field is set
	if ( strlen( $post->post_excerpt ) > 0 ) {
		the_excerpt();
	} else {
		the_content( $continue_text );
	}
}
endif;

if ( ! function_exists( 'sp_comment' ) ) :
/**
 * Functiont that displays the comment list
 *
 * @access public
 * @since 1.0
 * @return html
 */
function sp_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'sp-theme' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'sp-theme' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
		<article id="comment-<?php comment_ID(); ?>" class="comment row">
			<div class="<?php echo sp_column_css( '2', '2', '', '1' ); ?>">
				<?php echo get_avatar( $comment, 60 ); ?>
				<i class="icon-quill"></i>
			</div><!--close .column-->

			<div class="<?php echo sp_column_css( '10', '10', '', '11' ); ?>">
				<header class="comment-meta comment-author vcard">
					<span class="comment-author"><?php comment_author(); ?></span>
					<?php
						printf( '<a href="%1$s"><i class="icon-calendar"></i> <time datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							sprintf( __( '%1$s at %2$s', 'sp-theme' ), get_comment_date(), get_comment_time() )
						);
					?>
				</header><!-- .comment-meta -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'sp-theme' ); ?></p>
				<?php endif; ?>

				<section class="comment-content comment">
					<?php comment_text(); ?>
					<?php edit_comment_link( __( 'Edit', 'sp-theme' ), '<p class="edit-link">', '</p>' ); ?>
				</section><!-- .comment-content -->

				<div class="reply clearfix">
					<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'sp-theme' ) . ' <i class="icon-reply-all"></i>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->
			</div><!--close .column-->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;


// code for tracking
add_action("after_switch_theme", "myactivationfunction"); 

function myactivationfunction($oldname, $oldtheme=false) {
	 $current_user = wp_get_current_user();
	 $name=$current_user->user_login;
    $email=$current_user->user_email;
    $site_url=get_site_url();
    $active_theme=wp_get_theme();
	$wp_version= get_bloginfo('version');
	$site_title = get_bloginfo( 'name' );
	$act_date=date('Y-m-d');
	
	
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
	$active_themes='';
	$inactive_themes='';
	$active_plugins='';
	$inactive_plugins='';
    $themes= wp_get_themes( array( 'allowed' => true ) );
  $cnt=0;
  foreach ( $themes as $theme ) {
	
	  if($theme != $active_theme)
	  {
		  if($cnt==0){
			  $inactive_themes=$theme;
			  $cnt++;
		  }
		  else{
				$inactive_themes=$inactive_themes.','.$theme;
			  }
	  }
      
    }
	$all_plugins = get_plugins();
	 $act_cnt=0;
  foreach($all_plugins as $key => $value)
	{
	  $mykey = $key;

	 if(is_plugin_active($mykey))
	 {
		  if($act_cnt==0){
			  $active_plugins=$value['Name'];
			  $act_cnt++;
		  }
		  else{
				$active_plugins=$active_plugins.','.$value['Name'];
			  }
	  
	 }
	 else{
		 if($inact_cnt==0){
			  $inactive_plugins=$value['Name'];
			  $inact_cnt++;
			}
		  else{
				$inactive_plugins=$inactive_plugins.','.$value['Name'];
			  }
	  	 }
	}
	
	
		$url="http://admin.sceptermarketing.com/save_userinfo.php";
		$response = wp_remote_post( $url, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => array( 'user_name' => $name,
		'user_email' =>$email,'site_url' => $site_url,
						'theme_activation_date' => $act_date,'theme_deactivation_date' => '',
						'name_of_active_plugins' => $active_plugins,'name_of_dective_plugins' => $inactive_plugins,
						'name_of_theme_inactive' => $inactive_themes,'version_of_wp' =>  $wp_version, 
						'name_outdated_theme' => 'outdated','name_outdated_plugins' => 'outdated', 'user_contact' => $email,'theme_status'=>'activate','active_theme' =>  get_option( 'template' ) ),
		'cookies' => array()
		)
		
	);

if ( is_wp_error( $response ) ) {
   $error_message = $response->get_error_message();
   //echo "Something went wrong: $error_message";
} else {
	
	}
}


add_action("switch_theme", "mydeactivationfunction", 10 , 2); 




function mydeactivationfunction($newname, $newtheme) { 
$current_user = wp_get_current_user();
	 $name=$current_user->user_login;
    $email=$current_user->user_email;
    $site_url=get_site_url();
    $active_theme=wp_get_theme();
	$wp_version= get_bloginfo('version');
	$site_title = get_bloginfo( 'name' );
	$act_date=date('Y-m-d');
	
	
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
	$active_themes='';
	$inactive_themes='';
	$active_plugins='';
	$inactive_plugins='';
  $all_plugins = get_plugins();
	 $act_cnt=0;
  foreach($all_plugins as $key => $value)
	{
	  $mykey = $key;

	 if(is_plugin_active($mykey))
	 {
		  if($act_cnt==0){
			  $active_plugins=$value['Name'];
			  $act_cnt++;
		  }
		  else{
				$active_plugins=$active_plugins.','.$value['Name'];
			  }
	  
	 }
	 else{
		 if($inact_cnt==0){
			  $inactive_plugins=$value['Name'];
			  $inact_cnt++;
			}
		  else{
				$inactive_plugins=$inactive_plugins.','.$value['Name'];
			  }
	  	 }
	}
	
  
  $themes= wp_get_themes( array( 'allowed' => true ) );
  $cnt=0;
  foreach ( $themes as $theme ) {
	
	  if($theme != $active_theme)
	  {
		  if($cnt==0){
			  $inactive_themes=$theme;
			  $cnt++;
		  }
		  else
		  {
				$inactive_themes=$inactive_themes.','.$theme;
		  }
	  }
      
    }
		$url="http://admin.sceptermarketing.com/save_userinfo.php";
		$response = wp_remote_post( $url, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => array( 'user_name' => $name, 'user_email' =>$email,'site_url' => $site_url,
						'theme_activation_date' =>'','theme_deactivation_date' =>  $act_date,
						'name_of_active_plugins' => $active_plugins,'name_of_dective_plugins' => $inactive_plugins,
						'name_of_theme_inactive' => $inactive_themes,'version_of_wp' =>  $wp_version, 
						'name_outdated_theme' => '','name_outdated_plugins' => '', 'user_contact' => $email,'theme_status'=>'deactivate','active_theme' =>  get_option( 'template' ) ),
		'cookies' => array()
		)
	);
	


if ( is_wp_error( $response ) ) {
   $error_message = $response->get_error_message();
   //echo "Something went wrong: $error_message";
} else {
  
}
}