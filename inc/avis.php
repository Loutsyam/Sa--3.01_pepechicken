<?php
/**
 * IT-14 : avis clients
 *
 * US-14.1 : bloc « Avis clients » de la maquette sous « Ça va bien avec »
 *           (note moyenne, nombre d'avis, bouton « Laisser un avis », cartes des avis)
 *           templates : woocommerce/single-product-reviews.php et single-product/review-meta.php
 *           (HTML des avis de WooCommerce)
 * US-14.2 : réglages WooCommerce > Réglages > Produits > « Avis » : avis et notes activés,
 *           seulement par les clients qui ont acheté le produit (vérifié aussi par WooCommerce
 *           à l'envoi du formulaire)
 */

defined( 'ABSPATH' ) || exit;

// Plus d'onglet « Avis » : le bloc est affiché sous la fiche, comme sur la maquette
function pepe_retirer_onglet_avis( $onglets ) {
    unset( $onglets['reviews'] );
    return $onglets;
}
add_filter( 'woocommerce_product_tabs', 'pepe_retirer_onglet_avis', 98 );

function pepe_bloc_avis() {
    comments_template(); // charge woocommerce/single-product-reviews.php
}
add_action( 'woocommerce_after_single_product_summary', 'pepe_bloc_avis', 18 );

// Le visiteur peut-il laisser un avis ? (achat vérifié si l'option est cochée)
function pepe_peut_laisser_avis( $produit ) {
    if ( 'yes' !== get_option( 'woocommerce_review_rating_verification_required' ) ) {
        return true;
    }
    return is_user_logged_in() && wc_customer_bought_product( '', get_current_user_id(), $produit->get_id() );
}

// « Léa Dubois » => « Léa D. » (comme la maquette : Prénom N.)
function pepe_nom_avis( $nom ) {
    $morceaux = explode( ' ', trim( $nom ) );
    if ( count( $morceaux ) < 2 ) {
        return $nom;
    }
    return $morceaux[0] . ' ' . mb_substr( end( $morceaux ), 0, 1 ) . '.';
}

// Sur le site, le nom des auteurs d'avis est raccourci (« Prénom N. »)
function pepe_nom_auteur_avis( $nom, $comment_id, $comment ) {
    if ( ! is_admin() && $comment && 'review' === $comment->comment_type ) {
        return pepe_nom_avis( $nom );
    }
    return $nom;
}
add_filter( 'get_comment_author', 'pepe_nom_auteur_avis', 10, 3 );

// Pas de photo (gravatar) à côté des avis, comme sur la maquette
remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );

// Pas d'étoiles sous le titre du produit : la note est dans le bloc « Avis clients » (maquette)
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
