<?php
/**
 * Mon compte (IT-7) – surcharge de myaccount/my-account.php
 * « Bonjour Prénom », le menu à gauche et la page choisie à droite.
 *
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

// Prénom du compte, sinon celui de la dernière commande, sinon le nom affiché
$client = wp_get_current_user();
$prenom = $client->first_name;
if ( ! $prenom ) {
	$prenom = get_user_meta( $client->ID, 'billing_first_name', true );
}
if ( ! $prenom ) {
	$prenom = $client->display_name;
}
?>

<div class="pepe-compte">
	<h1 class="pepe-compte__titre">Bonjour <?php echo esc_html( $prenom ); ?> 👋</h1>

	<div class="pepe-compte__contenu">
		<?php do_action( 'woocommerce_account_navigation' ); ?>

		<div class="woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
