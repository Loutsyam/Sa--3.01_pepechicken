<?php
/**
 * Un moyen de paiement (IT-6) – surcharge de checkout/payment-method.php
 * Carte avec bouton radio : le titre, puis la description en dessous (maquette).
 *
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> pepe-paiement__choix">
	<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio pepe-paiement__radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>">

	<label class="pepe-paiement__texte" for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<span class="pepe-paiement__nom"><?php echo esc_html( $gateway->get_title() ); ?></span>
		<?php if ( $gateway->get_description() ) : ?>
			<span class="pepe-paiement__description"><?php echo wp_kses_post( $gateway->get_description() ); ?></span>
		<?php endif; ?>
	</label>

	<?php // Champs en plus (paiement par carte par exemple) : affichés sous la carte ?>
	<?php if ( $gateway->has_fields() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php echo $gateway->chosen ? '' : 'style="display:none;"'; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
