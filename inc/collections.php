<?php
/**
 * IT-12 : taxonomie « Collections »
 *
 * US-12.1 : taxonomie « Collections » créée dans SCF (acf-json/), sur les produits,
 *           indépendante des catégories ; menu Produits > Collections dans l'admin
 * US-12.3 : page de chaque collection (/collection/nom/) avec la grille de ses produits
 *           (template woocommerce/taxonomy-collection.php), liens depuis la Boutique
 *           et depuis la fiche produit (bloc « méta » de WooCommerce)
 */

defined( 'ABSPATH' ) || exit;

// Collections qui ont au moins un produit
function pepe_collections() {
    if ( ! taxonomy_exists( 'collection' ) ) {
        return array(); // taxonomie pas encore synchronisée dans SCF
    }

    $collections = get_terms( array(
        'taxonomy'   => 'collection',
        'hide_empty' => true,
    ) );

    if ( is_wp_error( $collections ) ) {
        return array();
    }
    return $collections;
}

// « Nos collections » (Boutique et page d'une collection) : même HTML que le widget
// « Nuage d'étiquettes produits » de WooCommerce (widget_product_tag_cloud, tagcloud, tag-cloud-link)
function pepe_liens_collections() {
    $collections = pepe_collections();
    if ( empty( $collections ) ) {
        return;
    }

    $courante = is_tax( 'collection' ) ? get_queried_object_id() : 0;

    echo '<nav class="widget woocommerce widget_product_tag_cloud" aria-label="Collections">';
    echo '<span class="gamma widget-title">Nos collections :</span>';
    echo '<div class="tagcloud">';

    foreach ( $collections as $collection ) {
        $actif = ( $courante === $collection->term_id );
        echo '<a class="tag-cloud-link" href="' . esc_url( get_term_link( $collection ) ) . '"' . ( $actif ? ' aria-current="page"' : '' ) . '>' . esc_html( $collection->name ) . '</a>';
    }

    echo '</div>';
    echo '</nav>';
}

// Sur la Boutique : sous les onglets des catégories
function pepe_collections_boutique() {
    if ( is_shop() ) {
        pepe_liens_collections();
    }
}
add_action( 'woocommerce_archive_description', 'pepe_collections_boutique', 6 );

// Titre de la page d'une collection
function pepe_titre_collection( $titre ) {
    if ( is_tax( 'collection' ) ) {
        return 'Collection ' . single_term_title( '', false );
    }
    return $titre;
}
add_filter( 'woocommerce_page_title', 'pepe_titre_collection' );

// Fiche produit : collections du produit dans le bloc « méta » de WooCommerce,
// à côté de la catégorie (même HTML : <span class="posted_in"> avec des liens rel="tag")
function pepe_collections_du_produit() {
    if ( ! taxonomy_exists( 'collection' ) ) {
        return;
    }

    $libelle = ( count( (array) get_the_terms( get_the_ID(), 'collection' ) ) > 1 ) ? 'Collections : ' : 'Collection : ';
    echo get_the_term_list( get_the_ID(), 'collection', '<span class="posted_in">' . esc_html( $libelle ), ', ', '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'woocommerce_product_meta_end', 'pepe_collections_du_produit' );
