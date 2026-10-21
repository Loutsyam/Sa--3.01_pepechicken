<?php
/**
 * Page d'une collection (IT-12 – US-12.3) : grille des produits de la collection
 * Basé sur woocommerce/templates/archive-product.php
 */
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );
?>
<header class="woocommerce-products-header">
	<h1 class="woocommerce-products-header__title page-title"><?php echo wp_kses_post( woocommerce_page_title( false ) ); ?></h1>
	<?php do_action( 'woocommerce_archive_description' ); // description de la collection ?>
	<?php pepe_liens_collections(); ?>
</header>
<?php
if ( woocommerce_product_loop() ) {
	do_action( 'woocommerce_before_shop_loop' );
	woocommerce_product_loop_start();
	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}
	woocommerce_product_loop_end();
	do_action( 'woocommerce_after_shop_loop' );
} else {
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
