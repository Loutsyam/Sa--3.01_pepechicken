<?php
/**
 * Tableau de bord du compte (IT-7) – surcharge de myaccount/dashboard.php
 * « Commande en cours » avec sa progression, puis les dernières commandes.
 * IT-9 : statut « Prête à retirer » (étape 3) et lien pour annuler la commande.
 *
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$commandes = wc_get_orders( array(
	'customer' => get_current_user_id(),
	'limit'    => 5,
	'orderby'  => 'date',
	'order'    => 'DESC',
) );

// La plus récente qui n'est pas encore récupérée
$en_cours = null;
foreach ( $commandes as $commande ) {
	if ( in_array( $commande->get_status(), array( 'pending', 'on-hold', 'processing', 'prete' ), true ) ) {
		$en_cours = $commande;
		break;
	}
}
?>

<?php if ( $en_cours ) : ?>
	<?php $etape = pepe_etape_commande( $en_cours ); ?>
	<section class="pepe-compte__carte pepe-suivi">
		<div class="pepe-suivi__entete">
			<h2 class="pepe-compte__sous-titre">Commande en cours · <?php echo esc_html( $en_cours->get_order_number() ); ?></h2>
			<span class="pepe-badge"><?php echo esc_html( pepe_statut_commande( $en_cours ) ); ?></span>
		</div>

		<p class="pepe-suivi__detail">
			<?php echo esc_html( $en_cours->get_shipping_method() . ' · ' . wc_format_datetime( $en_cours->get_date_created(), 'd/m/Y à H:i' ) ); ?>
		</p>

		<ol class="pepe-suivi__etapes" aria-label="Avancement de la commande">
			<?php foreach ( array( 'Reçue', 'En préparation', 'Prête', 'Récupérée' ) as $numero => $nom ) : ?>
				<li class="pepe-suivi__etape<?php echo $numero < $etape ? ' pepe-suivi__etape--faite' : ''; ?>">
					<span class="screen-reader-text"><?php echo esc_html( $nom ); ?></span>
				</li>
			<?php endforeach; ?>
		</ol>

		<?php // IT-9 – US-9.3 : annulation possible tant que la commande n'est pas prête ?>
		<?php if ( in_array( $en_cours->get_status(), array( 'on-hold', 'processing' ), true ) ) : ?>
			<p class="pepe-suivi__annuler">
				<a class="pepe-commandes__lien" href="<?php echo esc_url( $en_cours->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>">Annuler cette commande</a>
			</p>
		<?php endif; ?>
	</section>
<?php endif; ?>

<section class="pepe-compte__carte">
	<h2 class="pepe-compte__sous-titre">Mes commandes</h2>

	<?php if ( $commandes ) : ?>
		<table class="pepe-commandes">
			<thead>
				<tr>
					<th scope="col">Commande</th>
					<th scope="col">Date</th>
					<th scope="col">Statut</th>
					<th scope="col">Total</th>
					<th scope="col"><span class="screen-reader-text">Actions</span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $commandes as $commande ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( $commande->get_order_number() ); ?></th>
						<td><?php echo esc_html( wc_format_datetime( $commande->get_date_created(), 'd/m/Y' ) ); ?></td>
						<td><?php echo esc_html( pepe_statut_commande( $commande ) ); ?></td>
						<td><?php echo wp_kses_post( $commande->get_formatted_order_total() ); ?></td>
						<td><a class="pepe-commandes__lien" href="<?php echo esc_url( $commande->get_view_order_url() ); ?>">Voir</a></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p>Pas encore de commande. <a class="pepe-commandes__lien" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Découvrir la carte</a></p>
	<?php endif; ?>
</section>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
