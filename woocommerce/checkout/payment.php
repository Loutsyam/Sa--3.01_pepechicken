<?php
/**
 * Bloc « Paiement » (IT-6) – surcharge de checkout/payment.php
 * La div #payment garde la classe woocommerce-checkout-payment :
 * WooCommerce la recharge toute seule quand le total change.
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

	<div class="form-row place-order pepe-paiement__valider">
		<noscript>
			Votre navigateur n'exécute pas JavaScript : cliquez sur « Mettre à jour le total » avant de commander.
			<br><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="Mettre à jour le total">Mettre à jour le total</button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
