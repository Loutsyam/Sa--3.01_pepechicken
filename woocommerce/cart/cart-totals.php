<?php
/**
 * Récapitulatif du panier (IT-5, US-5.3 et US-5.4) – surcharge de cart/cart-totals.php
 *
 * La div garde la classe « cart_totals » : le script du panier de WooCommerce
 * la recharge quand on change de mode d'expédition ou de quantité.
 *
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals pepe-recap <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="pepe-recap__titre">Récapitulatif</h2>

	<?php if ( WC()->cart->needs_shipping() ) : ?>
		<?php pepe_choix_expedition(); ?>
	<?php endif; ?>

	<?php if ( wc_coupons_enabled() ) : ?>
		<div class="pepe-recap__bloc">
			<label class="pepe-recap__sous-titre" for="coupon_code">Code promo</label>
			<div class="pepe-promo">
				<?php // form="…" : ces champs envoient le formulaire du panier, même placés ici ?>
				<input type="text" form="pepe-form-panier" name="coupon_code" class="input-text pepe-promo__champ" id="coupon_code" value="" placeholder="Ex. PEPE10">
				<button type="submit" form="pepe-form-panier" class="button pepe-promo__bouton" name="apply_coupon" value="Appliquer">Appliquer</button>
			</div>
			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<?php // Lien « Retirer » : la classe woocommerce-remove-coupon est gérée par le script du panier ?>
				<p class="pepe-promo__applique">
					✓ Code <?php echo esc_html( strtoupper( $code ) ); ?> appliqué ·
					<a class="woocommerce-remove-coupon pepe-promo__retirer" href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $code ), wc_get_cart_url() ) ); ?>" data-coupon="<?php echo esc_attr( $code ); ?>">Retirer</a>
				</p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<table class="pepe-recap__totaux">
		<tr class="pepe-recap__ligne">
			<th scope="row">Sous-total</th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="pepe-recap__ligne">
				<th scope="row">Réduction (<?php echo esc_html( strtoupper( $code ) ); ?>)</th>
				<td>-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<tr class="pepe-recap__ligne">
				<th scope="row">Expédition</th>
				<td><?php echo WC()->cart->get_shipping_total() > 0 ? wc_price( WC()->cart->get_shipping_total() ) : 'Offerte'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="pepe-recap__ligne">
				<th scope="row"><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="pepe-recap__ligne pepe-recap__ligne--total">
			<th scope="row">Total</th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<p class="pepe-recap__note">Paiement bancaire ou au retrait en restaurant</p>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
