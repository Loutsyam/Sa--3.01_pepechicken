<?php
/**
 * Page Panier (IT-5, US-5.3) – surcharge du template WooCommerce cart/cart.php
 *
 * Même fonctionnement que le template d'origine (mêmes champs, mêmes classes
 * utilisées par le script du panier de WooCommerce : woocommerce-cart-form,
 * cart_item, product-remove, qty, update_cart), avec la mise en page de la
 * maquette : articles en cartes à gauche, récapitulatif à droite.
 *
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$nombre_articles = WC()->cart->get_cart_contents_count();
?>

<div class="pepe-panier">

	<header class="pepe-panier__entete">
		<h1 class="pepe-panier__titre">
			Mon panier
			<span class="pepe-panier__nombre"><?php echo esc_html( sprintf( _n( '%d article', '%d articles', $nombre_articles, 'pepe-chicken' ), $nombre_articles ) ); ?></span>
		</h1>

		<nav aria-label="Étapes de la commande">
			<ol class="pepe-etapes">
				<li class="pepe-etapes__item pepe-etapes__item--actif" aria-current="step">1 · Panier</li>
				<li class="pepe-etapes__item">2 · Retrait &amp; paiement</li>
				<li class="pepe-etapes__item">3 · Confirmation</li>
			</ol>
		</nav>
	</header>

	<div class="pepe-panier__contenu">

		<div class="pepe-panier__gauche">

			<form id="pepe-form-panier" class="woocommerce-cart-form pepe-panier__formulaire" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<ul class="pepe-panier__liste">
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>

					<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
						$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

						if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
							continue;
						}

						$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
						?>
						<li class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> pepe-article">

							<div class="pepe-article__image">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>

							<div class="pepe-article__infos">
								<h2 class="pepe-article__nom">
									<?php if ( $product_permalink ) : ?>
										<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $product_name ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $product_name ); ?>
									<?php endif; ?>
								</h2>

								<?php
								// Options choisies (IT-3) : « Menu · Creamy BBQ · Medium »
								$details = pepe_details_article_panier( $cart_item );
								if ( $details ) {
									echo '<p class="pepe-article__details">' . esc_html( $details ) . '</p>';
								}

								if ( $product_permalink && pepe_options_du_produit( $product_id ) ) {
									echo '<a class="pepe-article__modifier" href="' . esc_url( $product_permalink ) . '">Modifier les options</a>';
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
								?>
							</div>

							<div class="pepe-article__quantite product-quantity">
								<?php
								$max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();

								echo '<button type="button" class="pepe-quantite__bouton" data-pepe-quantite="-1" aria-label="Retirer un ' . esc_attr( $product_name ) . '">−</button>';

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $max_quantity,
										'min_value'    => 0,
										'product_name' => $product_name,
									),
									$_product,
									false
								);
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								echo '<button type="button" class="pepe-quantite__bouton" data-pepe-quantite="1" aria-label="Ajouter un ' . esc_attr( $product_name ) . '">+</button>';
								?>
							</div>

							<p class="pepe-article__prix">
								<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</p>

							<div class="pepe-article__supprimer product-remove">
								<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_attr( sprintf( 'Retirer %s du panier', wp_strip_all_tags( $product_name ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
								?>
							</div>
						</li>
						<?php
					}
					?>

					<?php do_action( 'woocommerce_cart_contents' ); ?>
				</ul>

				<?php // Bouton caché : utilisé par le script quand la quantité change ?>
				<button type="submit" class="button pepe-panier__maj" name="update_cart" value="Mettre à jour le panier">Mettre à jour le panier</button>
				<?php do_action( 'woocommerce_cart_actions' ); ?>
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</form>

			<?php pepe_suggestions_panier(); ?>

		</div>

		<aside class="pepe-panier__droite" aria-label="Récapitulatif de la commande">
			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</aside>

	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
