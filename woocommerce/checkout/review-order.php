<?php
/**
 * « Ma commande » (IT-6) – surcharge de checkout/review-order.php
 * Le tableau garde la classe woocommerce-checkout-review-order-table :
 * WooCommerce le recharge tout seul quand le total change.
 *
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table pepe-resume">
	<tbody>
		<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
			<?php
			$_product = $cart_item['data'];
			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
				continue;
			}
			$details = pepe_details_article_panier( $cart_item );
			?>
			<tr class="pepe-resume__ligne">
				<th scope="row">
					<?php echo esc_html( $cart_item['quantity'] . ' × ' . $_product->get_title() ); ?>
					<?php if ( $details ) : ?>
						<span class="pepe-resume__details"><?php echo esc_html( $details ); ?></span>
					<?php endif; ?>
				</th>
				<td><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="pepe-resume__ligne pepe-resume__ligne--separation">
			<th scope="row">Sous-total</th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="pepe-resume__ligne">
				<th scope="row">Réduction (<?php echo esc_html( strtoupper( $code ) ); ?>)</th>
				<td>-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endforeach; ?>

		<?php $methode = pepe_expedition_choisie(); ?>
		<?php if ( $methode ) : ?>
			<tr class="pepe-resume__ligne">
				<th scope="row"><?php echo esc_html( $methode->get_label() ); ?></th>
				<td><?php echo (float) $methode->get_cost() > 0 ? wc_price( $methode->get_cost() ) : 'Offert'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
			</tr>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="pepe-resume__ligne">
				<th scope="row"><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="pepe-resume__ligne pepe-resume__ligne--total">
			<th scope="row">Total</th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</tfoot>
</table>
