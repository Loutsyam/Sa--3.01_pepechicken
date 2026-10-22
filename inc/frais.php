<?php
/**
 * IT-15 : règles de tarification avancées – frais de préparation
 *
 * US-15.1 : montant, seuil et libellé réglés dans WooCommerce > Frais de préparation
 *           (page d'options et groupe de champs créés dans SCF, enregistrés dans acf-json/)
 * US-15.2 : les frais sont ajoutés au calcul du panier (crochet woocommerce_cart_calculate_fees)
 *           quand le montant des produits, après les codes promo, est sous le seuil
 * US-15.3 : la ligne apparaît ou devient « Offerts » à chaque mise à jour du panier
 */

defined( 'ABSPATH' ) || exit;

// Réglage de la page d'options (valeur par défaut du champ si la page n'a jamais été enregistrée)
function pepe_reglage_frais( $nom ) {
    if ( ! function_exists( 'get_field' ) ) {
        return null;
    }

    $valeur = get_field( $nom, 'option' );

    if ( null === $valeur ) {
        $champ  = acf_get_field( $nom );
        $valeur = $champ ? $champ['default_value'] : null;
    }
    return $valeur;
}

// Montant des produits du panier, codes promo déduits (sans livraison ni frais)
function pepe_montant_panier( $panier ) {
    $total = $panier->get_subtotal() + $panier->get_subtotal_tax();
    $total = $total - $panier->get_discount_total() - $panier->get_discount_tax();
    return $total;
}

// Les frais sont-ils actifs, et avec quels réglages ?
function pepe_frais_actifs() {
    $montant = (float) pepe_reglage_frais( 'frais_montant' );
    return pepe_reglage_frais( 'frais_actif' ) && $montant > 0;
}

/* ---------- US-15.2 : la règle dans le calcul du panier ---------- */

function pepe_ajouter_frais( $panier ) {
    if ( is_admin() && ! wp_doing_ajax() ) {
        return;
    }

    if ( ! pepe_frais_actifs() || $panier->is_empty() ) {
        return;
    }

    $montant = (float) pepe_reglage_frais( 'frais_montant' );
    $seuil   = (float) pepe_reglage_frais( 'frais_seuil' );

    if ( pepe_montant_panier( $panier ) < $seuil ) {
        $panier->add_fee( pepe_reglage_frais( 'frais_libelle' ), $montant, false );
    }
}
add_action( 'woocommerce_cart_calculate_fees', 'pepe_ajouter_frais' );

/* ---------- US-15.3 : « Offerts » et la phrase sous les frais ---------- */

// Lignes ajoutées dans le récapitulatif du panier et dans « Ma commande », avant le total
function pepe_lignes_frais() {
    if ( ! pepe_frais_actifs() || WC()->cart->is_empty() ) {
        return;
    }

    $libelle = pepe_reglage_frais( 'frais_libelle' );
    $montant = (float) pepe_reglage_frais( 'frais_montant' );
    $seuil   = (float) pepe_reglage_frais( 'frais_seuil' );
    $reste   = $seuil - pepe_montant_panier( WC()->cart );

    // Seuil atteint : WooCommerce n'a pas de ligne de frais, on affiche « Offerts »
    if ( $reste <= 0 ) {
        echo '<tr class="fee">';
        echo '<th scope="row">' . esc_html( $libelle ) . '</th>';
        echo '<td>Offerts</td>';
        echo '</tr>';
    }

    echo '<tr class="fee">';
    echo '<td colspan="2"><small>';
    echo esc_html( $libelle . ' de ' . wp_strip_all_tags( wc_price( $montant ) ) . ' offerts dès ' . wp_strip_all_tags( wc_price( $seuil ) ) . ' d’achat' );
    if ( $reste > 0 ) {
        echo '<br>Plus que ' . esc_html( wp_strip_all_tags( wc_price( $reste ) ) ) . ' pour en profiter.';
    }
    echo '</small></td>';
    echo '</tr>';
}
add_action( 'woocommerce_cart_totals_before_order_total', 'pepe_lignes_frais' );
add_action( 'woocommerce_review_order_before_order_total', 'pepe_lignes_frais' );
