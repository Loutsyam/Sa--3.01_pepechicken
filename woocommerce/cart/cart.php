<?php
/**
 * Page Panier (IT-5, US-5.3) – surcharge du template WooCommerce cart/cart.php
 *
 * Même HTML que le template d'origine (tableau shop_table cart, colonnes product-thumbnail,
 * product-name, product-quantity, product-subtotal, product-remove…) : le style.css
 * l'affiche en cartes comme sur la maquette. Ajouts : les options choisies sous le nom,
 * les boutons − / + autour de la quantité, et « Un petit plus ? » sous les articles.
 *
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form id="pepe-form-panier" class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents">
		<thead>
			<tr>
				<th scope="col" class="product-thumbnail"><span class="screen-reader-text">Image</span></th>
				<th scope="col" class="product-name">Produit</th>
				<th scope="col" class="product-quantity">Quantité</th>
				<th scope="col" class="product-subtotal">Total</th>
				<th scope="col" class="product-remove"><span class="screen-reader-text">Retirer</span></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					continue;
				}

				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); // get_title : nom sans la variation (affichée dessous)
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
				?>
				<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-thumbnail">
						<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>

					<td class="product-name" data-title="Produit">
						<?php if ( $product_permalink ) : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo esc_html( $product_name ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $product_name ); ?>
						<?php endif; ?>

						<?php
						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Options choisies (IT-3) : « Menu · Creamy BBQ · Medium »
						$details = pepe_details_article_panier( $cart_item );
						if ( $details ) {
							echo '<p class="variation">' . esc_html( $details ) . '</p>';
						}

						if ( $product_permalink && pepe_options_du_produit( $product_id ) ) {
							echo '<small><a href="' . esc_url( $product_permalink ) . '">Modifier les options</a></small>';
						}
						?>
					</td>

					<td class="product-quantity" data-title="Quantité">
						<?php
						$max_quantity = $_product->is_sold_individually() ? 1 : $_product->get_max_purchase_quantity();

						// Boutons − / + : mêmes noms de classes que le sélecteur de quantité des blocs WooCommerce
						echo '<button type="button" class="wc-block-components-quantity-selector__button wc-block-components-quantity-selector__button--minus" aria-label="Retirer un ' . esc_attr( $product_name ) . '">−</button>';

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

						echo '<button type="button" class="wc-block-components-quantity-selector__button wc-block-components-quantity-selector__button--plus" aria-label="Ajouter un ' . esc_attr( $product_name ) . '">+</button>';
						?>
					</td>

					<td class="product-subtotal" data-title="Total">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>

					<td class="product-remove">
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
					</td>
				</tr>
				<?php
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="5" class="actions">
					<?php // Bouton caché : utilisé par le script quand la quantité change. Le code promo est dans le récapitulatif ?>
					<button type="submit" class="button" name="update_cart" value="Mettre à jour le panier">Mettre à jour le panier</button>
					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); // « Un petit plus ? » (inc/panier.php) ?>

<div class="cart-collaterals">
	<?php do_action( 'woocommerce_cart_collaterals' ); // récapitulatif : cart/cart-totals.php ?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
