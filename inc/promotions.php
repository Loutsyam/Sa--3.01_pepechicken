<?php
/**
 * IT-10 : promotions
 *
 * US-10.1 : codes promo créés dans Marketing > Codes promo (montant fixe et pourcentage)
 * US-10.2 : affichage des promotions comme la maquette (badge « Promo », prix barré,
 *           réduction dans le récapitulatif du panier, de la commande et des e-mails)
 * US-10.3 : messages clairs quand on ajoute, retire ou tape un code erroné / expiré / indisponible
 */

defined( 'ABSPATH' ) || exit;


/* ---------- US-10.2 : badge « Promo » ---------- */

function pepe_badge_promo( $html, $post, $produit ) {
    return '<span class="onsale">Promo</span>';
}
add_filter( 'woocommerce_sale_flash', 'pepe_badge_promo', 10, 3 );


/* ---------- US-10.2 : « Tu économises … » sous le total du panier ---------- */

function pepe_economie_panier() {
    $economie = WC()->cart->get_discount_total();

    if ( $economie <= 0 ) {
        return;
    }

    // Ligne sous le total, même classe que les réductions de WooCommerce
    echo '<tr class="cart-discount">';
    echo '<th scope="row">Tu économises</th>';
    echo '<td>' . wp_kses_post( wc_price( $economie ) ) . '</td>';
    echo '</tr>';
}
add_action( 'woocommerce_cart_totals_after_order_total', 'pepe_economie_panier' );


/* ---------- US-10.3 : messages des codes promo ---------- */

// Code ajouté ou retiré
function pepe_message_code_promo( $message, $code_message, $coupon ) {
    if ( WC_Coupon::WC_COUPON_SUCCESS === $code_message ) {
        return 'Code promo ' . strtoupper( $coupon->get_code() ) . ' appliqué !';
    }
    if ( WC_Coupon::WC_COUPON_REMOVED === $code_message ) {
        return 'Code promo retiré.';
    }
    return $message;
}
add_filter( 'woocommerce_coupon_message', 'pepe_message_code_promo', 10, 3 );

// Code refusé : on dit pourquoi
function pepe_erreur_code_promo( $erreur, $code_erreur, $coupon ) {
    $code = $coupon ? strtoupper( $coupon->get_code() ) : '';

    switch ( $code_erreur ) {
        case WC_Coupon::E_WC_COUPON_NOT_EXIST:
        case WC_Coupon::E_WC_COUPON_INVALID_FILTERED:
            return 'Le code ' . $code . ' n\'existe pas. Vérifie qu\'il est bien écrit.';
        case WC_Coupon::E_WC_COUPON_EXPIRED:
            return 'Le code ' . $code . ' a expiré.';
        case WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED:
            return 'Le code ' . $code . ' n\'est plus disponible : il a déjà été utilisé le nombre de fois prévu.';
        case WC_Coupon::E_WC_COUPON_NOT_APPLICABLE:
        case WC_Coupon::E_WC_COUPON_EXCLUDED_PRODUCTS:
        case WC_Coupon::E_WC_COUPON_EXCLUDED_CATEGORIES:
            return 'Le code ' . $code . ' n\'est pas valable pour les produits de ton panier.';
        case WC_Coupon::E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET:
            return 'Le code ' . $code . ' demande un montant minimum de ' . wp_strip_all_tags( wc_price( $coupon->get_minimum_amount() ) ) . '.';
        case WC_Coupon::E_WC_COUPON_ALREADY_APPLIED:
            return 'Le code ' . $code . ' est déjà appliqué.';
        case WC_Coupon::E_WC_COUPON_PLEASE_ENTER:
            return 'Entre un code promo.';
    }
    return $erreur;
}
add_filter( 'woocommerce_coupon_error', 'pepe_erreur_code_promo', 10, 3 );

// Retrait avec le lien « Retirer » du panier (message écrit directement par WooCommerce)
function pepe_message_code_retire( $message ) {
    if ( __( 'Coupon has been removed.', 'woocommerce' ) === $message ) {
        return 'Code promo retiré.';
    }
    return $message;
}
add_filter( 'woocommerce_add_success', 'pepe_message_code_retire' );
