<?php
/**
 * Bloc « Paiement » (IT-6) – surcharge de checkout/payment.php
 * La div #payment garde la classe woocommerce-checkout-payment :
 * WooCommerce la recharge toute seule quand le total change.
 * Le bouton « Passer la commande » est sous « Ma commande », à droite.
 *
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment pepe-paiement">
	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<section class="pepe-commande__carte">
			<h2 class="pepe-commande__titre">Paiement</h2>
			<ul class="wc_payment_methods payment_methods methods pepe-paiement__liste" aria-label="Moyens de paiement">
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
		</section>
	<?php endif; ?>

	<?php // Le bouton « Passer la commande » est dans la colonne de droite (form-checkout.php) ?>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
