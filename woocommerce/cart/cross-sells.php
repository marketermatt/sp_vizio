<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $product;

$cross_sell_product_count = sp_get_option( 'cross_sell_product_count' );

if ( isset( $cross_sell_product_count ) && ! empty( $cross_sell_product_count ) )
	$posts_per_page = absint( $cross_sell_product_count );

$crosssells = WC()->cart->get_cross_sells();

if ( sizeof( $crosssells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'posts_per_page' 		=> apply_filters( 'sp_woo_cross_sell_product_count', $posts_per_page ),
	'orderby' 				=> apply_filters( 'sp_woo_cross_sell_product_orderby', 'rand' ),
	'no_found_rows'       => 1,
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$title = sp_get_option( 'upsell_product_title' );

if ( ! isset( $title ) || empty( $title ) )
	$title = '';

if ( $products->have_posts() ) : ?>

	<div class="cross-sells products">

		<h2 class="title-with-line"><span><?php echo $title; ?></span></h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'cross-sell-product' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>

<?php endif;

wp_reset_query();