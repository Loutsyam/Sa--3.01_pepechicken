<?php
/**
 * Récapitulatif du panier (IT-5, US-5.3 et US-5.4) – surcharge de cart/cart-totals.php
 *
 * Même HTML que WooCommerce (cart_totals, shop_table, cart-subtotal, cart-discount, fee,
 * order-total, wc-proceed-to-checkout) : le script du panier recharge ce bloc quand on change
 * de mode d'expédition ou de quantité. Ajouts de la maquette : le mode de récupération
 * en cartes et le code promo au-dessus des totaux.
 *
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2>Récapitulatif</h2>

	<?php
	if ( WC()->cart->needs_shipping() ) {
		pepe_choix_expedition(); // liste #shipping_method de WooCommerce (inc/panier.php)
	}
	?>

	<?php if ( wc_coupons_enabled() ) : ?>
		<div class="coupon">
			<h3><label for="coupon_code">Code promo</label></h3>
			<?php // form="…" : ces champs envoient le formulaire du panier, même placés ici ?>
			<input type="text" form="pepe-form-panier" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Ex. PEPE10">
			<button type="submit" form="pepe-form-panier" class="button" name="apply_coupon" value="Appliquer">Appliquer</button>

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<?php
				// Réduction du code, comme la maquette : « -10 % » ou « -2,00 € »
				if ( 'percent' === $coupon->get_discount_type() ) {
					$reduction = '-' . wc_format_localized_decimal( $coupon->get_amount() ) . ' %';
				} else {
					$reduction = '-' . wp_strip_all_tags( wc_price( $coupon->get_amount() ) );
				}
				?>
				<p>
					✓ Code <?php echo esc_html( strtoupper( $code ) ); ?> appliqué : <?php echo esc_html( $reduction ); ?> ·
					<?php // la classe woocommerce-remove-coupon est gérée par le script du panier ?>
					<a class="woocommerce-remove-coupon" href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_cart_url() ) ); ?>" data-coupon="<?php echo esc_attr( $code ); ?>">Retirer <span aria-hidden="true">✕</span></a>
				</p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<table class="shop_table shop_table_responsive">

		<tr class="cart-subtotal">
			<th>Sous-total</th>
			<td data-title="Sous-total"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th>Réduction (<?php echo esc_html( strtoupper( $code ) ); ?>)</th>
				<td data-title="Réduction">-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<tr class="woocommerce-shipping-totals shipping">
				<th>Expédition</th>
				<td data-title="Expédition"><?php echo WC()->cart->get_shipping_total() > 0 ? wc_price( WC()->cart->get_shipping_total() ) : 'Offerte'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total">
			<th>Total</th>
			<td data-title="Total"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
		<p><small>Paiement bancaire ou au retrait en restaurant</small></p>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
