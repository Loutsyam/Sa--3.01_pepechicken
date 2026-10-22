<?php
/**
 * Page de validation de commande (IT-6) – surcharge de checkout/form-checkout.php
 *
 * Même HTML que le template de WooCommerce (#customer_details col2-set, #order_review_heading,
 * #order_review, #payment, place-order) : Storefront met les coordonnées à gauche et « Ma commande »
 * à droite. Différences (maquette) : retrait et paiement à gauche, bouton sous « Ma commande ».
 *
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout ); // étapes de la commande (inc/commande.php)

// Si un compte est obligatoire pour commander et que le visiteur n'est pas connecté
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="Validation de la commande">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

	<h2 id="order_review_heading">Ma commande</h2>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

	<?php
	// Retrait et paiement : écrits après « Ma commande » pour que les floats de Storefront
	// laissent « Ma commande » en haut à droite ; ils s'affichent à gauche, sous les coordonnées.
	// Le bouton « Passer la commande » est dans #order_review, comme dans WooCommerce (inc/commande.php)
	pepe_bloc_retrait();
	woocommerce_checkout_payment();
	?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
