<?php
/**
 * Page de validation de commande (IT-6) – surcharge de checkout/form-checkout.php
 *
 * Mêmes actions WooCommerce que le template d'origine, rangées comme la maquette :
 * coordonnées, retrait et paiement à gauche, « Ma commande » à droite.
 *
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// Si un compte est obligatoire pour commander et que le visiteur n'est pas connecté
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="pepe-commande">

	<header class="pepe-panier__entete">
		<h1 class="pepe-panier__titre">Finaliser ma commande</h1>
		<nav aria-label="Étapes de la commande">
			<ol class="pepe-etapes">
				<li class="pepe-etapes__item">1 · Panier</li>
				<li class="pepe-etapes__item pepe-etapes__item--actif" aria-current="step">2 · Retrait &amp; paiement</li>
				<li class="pepe-etapes__item">3 · Confirmation</li>
			</ol>
		</nav>
	</header>

	<form name="checkout" method="post" class="checkout woocommerce-checkout pepe-commande__contenu" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="Validation de la commande">

		<div class="pepe-commande__gauche">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<section class="pepe-commande__carte" id="customer_details">
					<h2 class="pepe-commande__titre">Mes coordonnées</h2>

					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>

					<?php if ( ! is_user_logged_in() ) : ?>
						<p class="pepe-commande__connexion">Déjà client ? <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Se connecter</a></p>
					<?php endif; ?>
				</section>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			<?php woocommerce_checkout_payment(); ?>

		</div>

		<aside class="pepe-commande__droite" aria-label="Ma commande">
			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order pepe-commande__carte">
				<h2 class="pepe-commande__titre" id="order_review_heading">Ma commande</h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			<?php // Bouton « Passer la commande » sous le récapitulatif (maquette) ?>
			<div class="form-row place-order pepe-paiement__valider">
				<noscript>
					Votre navigateur n'exécute pas JavaScript : cliquez sur « Mettre à jour le total » avant de commander.
					<br><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="Mettre à jour le total">Mettre à jour le total</button>
				</noscript>

				<?php wc_get_template( 'checkout/terms.php' ); ?>

				<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

				<?php $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ); ?>
				<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

				<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
			</div>
		</aside>

	</form>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
