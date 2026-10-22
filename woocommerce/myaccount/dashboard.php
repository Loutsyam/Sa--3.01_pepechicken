<?php
/**
 * Tableau de bord du compte (IT-7) – surcharge de myaccount/dashboard.php
 * « Commande en cours » avec sa progression, puis les dernières commandes.
 * IT-9 : statut « Prête à retirer » (étape 3) et lien pour annuler la commande.
 *
 * Mêmes classes que WooCommerce : woocommerce-order, mark.order-status,
 * tableau woocommerce-orders-table (comme la page « Mes commandes »), boutons button.
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
	<section class="woocommerce-order">
		<h2>
			Commande en cours · <?php echo esc_html( $en_cours->get_order_number() ); ?>
			<mark class="order-status"><?php echo esc_html( pepe_statut_commande( $en_cours ) ); ?></mark>
		</h2>

		<p>
			<small><?php echo esc_html( $en_cours->get_shipping_method() . ' · ' . wc_format_datetime( $en_cours->get_date_created(), 'd/m/Y à H:i' ) ); ?></small>
		</p>

		<?php // Barre d'avancement : balise HTML progress (étape sur 4 : reçue, en préparation, prête, récupérée) ?>
		<progress max="4" value="<?php echo esc_attr( $etape ); ?>" aria-label="Avancement de la commande">Étape <?php echo esc_html( $etape ); ?> sur 4</progress>

		<?php // IT-9 – US-9.3 : annulation possible tant que la commande n'est pas prête ?>
		<?php if ( in_array( $en_cours->get_status(), array( 'on-hold', 'processing' ), true ) ) : ?>
			<p>
				<a class="woocommerce-button button cancel" href="<?php echo esc_url( $en_cours->get_cancel_order_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>">Annuler cette commande</a>
			</p>
		<?php endif; ?>
	</section>
<?php endif; ?>

<section class="woocommerce-orders">
	<h2>Mes commandes</h2>

	<?php if ( $commandes ) : ?>
		<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
			<thead>
				<tr>
					<th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number">Commande</th>
					<th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date">Date</th>
					<th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">Statut</th>
					<th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total">Total</th>
					<th scope="col" class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="screen-reader-text">Actions</span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $commandes as $commande ) : ?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $commande->get_status() ); ?> order">
						<th scope="row" class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Commande"><?php echo esc_html( $commande->get_order_number() ); ?></th>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Date"><?php echo esc_html( wc_format_datetime( $commande->get_date_created(), 'd/m/Y' ) ); ?></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="Statut"><?php echo esc_html( pepe_statut_commande( $commande ) ); ?></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total"><?php echo wp_kses_post( $commande->get_formatted_order_total() ); ?></td>
						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions"><a class="woocommerce-button button view" href="<?php echo esc_url( $commande->get_view_order_url() ); ?>">Voir</a></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p class="woocommerce-info">Pas encore de commande. <a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Découvrir la carte</a></p>
	<?php endif; ?>
</section>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
