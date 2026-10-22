<?php
/**
 * Bloc « Paiement » (IT-6) – surcharge de checkout/payment.php
 * Même HTML que WooCommerce (#payment, wc_payment_methods) : WooCommerce le recharge tout seul
 * quand le total change. Le bouton « Passer la commande » est sous « Ma commande » (form-checkout.php).
 *
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<h2>Paiement</h2>
		<ul class="wc_payment_methods payment_methods methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li>';
				wc_print_notice( 'Aucun moyen de paiement disponible pour le moment.', 'notice' );
				echo '</li>';
			}
			?>
		</ul>
	<?php endif; ?>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
