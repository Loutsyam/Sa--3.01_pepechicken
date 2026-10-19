<?php
/**
 * Bouton « Valider ma commande » du panier (IT-5, US-5.3)
 * Surcharge de cart/proceed-to-checkout-button.php : texte de la maquette.
 *
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward pepe-btn pepe-btn--rouge pepe-recap__valider">
	Valider ma commande
</a>
