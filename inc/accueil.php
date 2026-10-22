<?php
/**
 * IT-2 – US-2.2 : page d'accueil (« Le concept ») conforme à la maquette
 *
 * Template : front-page.php
 * Textes et images réglables dans l'admin : groupe de champs SCF « Page d'accueil »
 * (acf-json/), affiché sur la page choisie comme page d'accueil
 * (Réglages > Lecture > « Une page statique »).
 */

defined( 'ABSPATH' ) || exit;

// Champ SCF de la page d'accueil (valeur par défaut du champ si rien n'est saisi)
function pepe_accueil( $nom ) {
    if ( ! function_exists( 'get_field' ) ) {
        return '';
    }
    $page   = get_option( 'page_on_front' );
    $valeur = get_field( $nom, $page ? $page : false );

    // Champ jamais enregistré : on prend le texte par défaut réglé dans SCF
    if ( null === $valeur || '' === $valeur ) {
        $champ  = acf_get_field( $nom );
        $valeur = ( $champ && isset( $champ['default_value'] ) ) ? $champ['default_value'] : '';

        // Le texte du concept est écrit avec des lignes vides : on fait les paragraphes
        if ( $champ && 'textarea' === $champ['type'] && 'wpautop' === $champ['new_lines'] ) {
            $valeur = wpautop( $valeur );
        }
    }
    return $valeur;
}

// Script du carrousel « Découvre nos classiques »
function pepe_script_accueil() {
    if ( is_front_page() ) {
        wp_enqueue_script( 'pepe-accueil', get_stylesheet_directory_uri() . '/js/accueil.js', array(), wp_get_theme()->get( 'Version' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'pepe_script_accueil' );

// « Nos nouveautés » : produits choisis dans l'admin, sinon les 3 derniers
function pepe_nouveautes() {
    $ids = pepe_accueil( 'accueil_nouveautes' );

    if ( empty( $ids ) ) {
        $ids = wc_get_products( array(
            'status'  => 'publish',
            'limit'   => 3,
            'orderby' => 'date',
            'order'   => 'DESC',
            'return'  => 'ids',
        ) );
    }
    return $ids;
}

// « C'est validé ! » : derniers avis 5 étoiles des clients (IT-14)
function pepe_avis_accueil( $nombre ) {
    return get_comments( array(
        'type'       => 'review',
        'status'     => 'approve',
        'number'     => $nombre,
        'meta_key'   => 'rating', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
        'meta_value' => 5,        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
    ) );
}

// Une carte d'avis de la page d'accueil : même HTML qu'un avis de WooCommerce
// (li.review, comment_container, avatar, comment-text, star-rating, meta, description)
// $alt = true : carte rouge (classe alt, comme les boutons rouges de WooCommerce)
function pepe_carte_avis_accueil( $avis, $alt ) {
    echo '<li class="review' . ( $alt ? ' alt' : '' ) . '">';
    echo '<div class="comment_container">';
    echo get_avatar( $avis, 40 );
    echo '<div class="comment-text">';
    echo wc_get_rating_html( 5 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<p class="meta"><strong class="woocommerce-review__author">' . esc_html( pepe_nom_avis( get_comment_author( $avis ) ) ) . '</strong></p>';
    echo '<div class="description"><p>' . esc_html( wp_trim_words( $avis->comment_content, 18 ) ) . '</p></div>';
    echo '</div>';
    echo '</div>';
    echo '</li>';
}

// « Ton burger favori ? » : les 3 burgers les plus vendus, en pourcentage
function pepe_sondage_burgers() {
    $burgers = wc_get_products( array(
        'status'   => 'publish',
        'category' => array( 'burgers' ),
        'limit'    => 3,
        'orderby'  => 'popularity',
    ) );

    $total = 0;
    foreach ( $burgers as $burger ) {
        $total = $total + $burger->get_total_sales();
    }

    $resultats = array();
    if ( $total > 0 ) {
        foreach ( $burgers as $burger ) {
            $resultats[ $burger->get_name() ] = round( $burger->get_total_sales() * 100 / $total );
        }
    }
    return $resultats;
}
