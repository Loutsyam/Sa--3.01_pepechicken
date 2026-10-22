<?php
/**
 * Un moyen de paiement (IT-6) – surcharge de checkout/payment-method.php
 * Même HTML que WooCommerce ; la description est affichée sous le titre, dans le label (maquette).
 *
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>">

	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<?php echo esc_html( $gateway->get_title() ); ?>
		<?php if ( $gateway->get_description() ) : ?>
			<small><?php echo wp_kses_post( $gateway->get_description() ); ?></small>
		<?php endif; ?>
	</label>

	<?php // Champs en plus (paiement par carte par exemple) : affichés sous le moyen de paiement ?>
	<?php if ( $gateway->has_fields() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php echo $gateway->chosen ? '' : 'style="display:none;"'; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
