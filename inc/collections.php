<?php
/**
 * IT-12 : taxonomie « Collections »
 *
 * US-12.1 : taxonomie « Collections » créée dans SCF (acf-json/), sur les produits,
 *           indépendante des catégories ; menu Produits > Collections dans l'admin
 * US-12.3 : page de chaque collection (/collection/nom/) avec la grille de ses produits
 *           (template woocommerce/taxonomy-collection.php), liens depuis la Boutique
 *           et depuis la fiche produit
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

// Pilules « Nos collections » (Boutique et page d'une collection)
function pepe_liens_collections() {
    $collections = pepe_collections();
    if ( empty( $collections ) ) {
        return;
    }

    $courante = is_tax( 'collection' ) ? get_queried_object_id() : 0;

    echo '<nav class="pepe-categories pepe-categories--collections" aria-label="Collections">';
    echo '<span class="pepe-categories__intro">Nos collections :</span>';

    foreach ( $collections as $collection ) {
        $url = get_term_link( $collection );

        if ( $courante === $collection->term_id ) {
            echo '<a class="pepe-categories__lien pepe-categories__lien--actif" href="' . esc_url( $url ) . '" aria-current="page">' . esc_html( $collection->name ) . '</a>';
        } else {
            echo '<a class="pepe-categories__lien" href="' . esc_url( $url ) . '">' . esc_html( $collection->name ) . '</a>';
        }
    }

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

// Fiche produit : collections du produit, sous le titre
function pepe_collections_du_produit() {
    if ( ! taxonomy_exists( 'collection' ) ) {
        return;
    }

    $collections = get_the_terms( get_the_ID(), 'collection' );
    if ( empty( $collections ) || is_wp_error( $collections ) ) {
        return;
    }

    $libelle = ( count( $collections ) > 1 ) ? 'Collections : ' : 'Collection : ';
    echo '<p class="pepe-collections-produit">' . esc_html( $libelle );
    $liens = array();
    foreach ( $collections as $collection ) {
        $liens[] = '<a class="pepe-collections-produit__lien" href="' . esc_url( get_term_link( $collection ) ) . '">' . esc_html( $collection->name ) . '</a>';
    }
    echo implode( ' ', $liens ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</p>';
}
add_action( 'woocommerce_single_product_summary', 'pepe_collections_du_produit', 6 );
