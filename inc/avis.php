<?php
/**
 * IT-14 : avis clients
 *
 * US-14.1 : bloc « Avis clients » de la maquette sous « Ça va bien avec »
 *           (note moyenne, nombre d'avis, bouton « Laisser un avis », cartes des avis)
 *           template : woocommerce/single-product-reviews.php
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

// Une carte d'avis (appelée par wp_list_comments, le <li> est fermé par WordPress)
function pepe_carte_avis( $avis, $args, $profondeur ) {
    $note    = intval( get_comment_meta( $avis->comment_ID, 'rating', true ) );
    $verifie = wc_review_is_from_verified_owner( $avis->comment_ID );
    ?>
    <li id="li-comment-<?php comment_ID(); ?>" <?php comment_class( 'pepe-avis__carte' ); ?>>
        <article id="comment-<?php comment_ID(); ?>">
            <header class="pepe-avis__entete">
                <strong class="pepe-avis__auteur"><?php echo esc_html( pepe_nom_avis( get_comment_author( $avis ) ) ); ?></strong>
                <?php if ( $note && wc_review_ratings_enabled() ) : ?>
                    <?php echo wc_get_rating_html( $note ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endif; ?>
            </header>

            <?php if ( '0' === $avis->comment_approved ) : ?>
                <p class="pepe-avis__attente">Merci ! Ton avis sera visible dès qu'il aura été validé.</p>
            <?php endif; ?>

            <div class="pepe-avis__texte"><?php comment_text(); ?></div>

            <p class="pepe-avis__infos">
                <?php
                if ( $verifie ) {
                    echo 'Achat vérifié · ';
                }
                echo 'il y a ' . esc_html( human_time_diff( get_comment_date( 'U', $avis ), time() ) );
                ?>
            </p>
        </article>
    <?php
}
