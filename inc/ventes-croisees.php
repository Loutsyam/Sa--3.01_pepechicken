<?php
/**
 * IT-13 : ventes croisées
 *
 * US-13.1 : dans l'admin, fiche produit > Données produit > Produits liés > « Ventes croisées »
 *           (produits complémentaires : sauce, side, boisson…)
 * US-13.2 : affichage comme la maquette
 *           - fiche produit : section « Ça va bien avec » (4 produits, cartes de la Boutique)
 *           - panier : « Un petit plus ? » (inc/panier.php, pepe_suggestions_panier)
 * US-13.3 : les suggestions ne reprennent jamais un produit déjà dans le panier
 */

defined( 'ABSPATH' ) || exit;

// On remplace « Produits apparentés » et « Vous aimerez peut-être aussi » de WooCommerce
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Produits complémentaires d'un produit : ses ventes croisées, sinon ses produits suggérés
function pepe_produits_complementaires( $produit, $nombre ) {
    $ids = $produit->get_cross_sell_ids();

    if ( empty( $ids ) ) {
        $ids = $produit->get_upsell_ids();
    }

    // Pas ceux qui sont déjà dans le panier
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $article ) {
            $ids = array_diff( $ids, array( $article['product_id'] ) );
        }
    }

    $produits = array();
    foreach ( $ids as $id ) {
        $complement = wc_get_product( $id );
        if ( $complement && $complement->is_visible() ) {
            $produits[] = $complement;
        }
    }

    return array_slice( $produits, 0, $nombre );
}

// Fiche produit : « Ça va bien avec »
function pepe_ca_va_bien_avec() {
    global $product;

    $produits = pepe_produits_complementaires( $product, 4 );
    if ( empty( $produits ) ) {
        return;
    }

    echo '<section class="pepe-complements" aria-labelledby="pepe-complements-titre">';
    echo '<h2 class="pepe-complements__titre" id="pepe-complements-titre">Ça va bien avec</h2>';

    // Mêmes cartes que la Boutique (content-product.php), 4 par ligne
    wc_set_loop_prop( 'name', 'complements' );
    wc_set_loop_prop( 'columns', 4 );
    woocommerce_product_loop_start();

    foreach ( $produits as $complement ) {
        $GLOBALS['post'] = get_post( $complement->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
        setup_postdata( $GLOBALS['post'] );
        wc_get_template_part( 'content', 'product' );
    }

    woocommerce_product_loop_end();
    wp_reset_postdata();

    echo '</section>';
}
add_action( 'woocommerce_after_single_product_summary', 'pepe_ca_va_bien_avec', 15 );
