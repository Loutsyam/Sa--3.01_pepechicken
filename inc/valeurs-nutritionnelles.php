<?php
/**
 * US-4 : valeurs nutritionnelles et allergènes sur la fiche produit
 *
 * Les valeurs sont des champs SCF (groupe « Valeurs nutritionnelles »,
 * enregistré dans acf-json/). Elles s'affichent sous le titre du produit,
 * juste au-dessus du prix.
 */

defined( 'ABSPATH' ) || exit;

/* Champs SCF du groupe, dans l'ordre d'affichage : nom du champ => unité */
function pepe_champs_nutrition() {
    return array(
        'pepe_energie'             => 'kcal',
        'pepe_matieres_grasses'    => 'g',
        'pepe_acides_gras_satures' => 'g',
        'pepe_glucides'            => 'g',
        'pepe_sucres'              => 'g',
        'pepe_fibres'              => 'g',
        'pepe_proteines'           => 'g',
        'pepe_sel'                 => 'g',
    );
}

/**
 * Affichage sous le titre (priorité 5) et avant le prix (priorité 10)
 * dans le résumé de la fiche produit WooCommerce.
 */
function pepe_afficher_nutrition() {
    if ( ! function_exists( 'get_field_object' ) ) {
        return; // SCF n'est pas activé
    }

    // On prépare les lignes remplies
    $lignes = array();
    foreach ( pepe_champs_nutrition() as $nom => $unite ) {
        $champ = get_field_object( $nom );

        if ( ! $champ || '' === (string) $champ['value'] ) {
            continue; // valeur non renseignée
        }

        $valeur = (float) $champ['value'];
        $decimales = ( floor( $valeur ) == $valeur ) ? 0 : 1;

        $lignes[] = array(
            'nom'    => $champ['label'],
            'valeur' => number_format_i18n( $valeur, $decimales ) . ' ' . $unite,
        );
    }

    $allergenes = get_field( 'pepe_allergenes' );

    if ( empty( $lignes ) && empty( $allergenes ) ) {
        return; // rien à afficher pour ce produit
    }

    echo '<div class="pepe-nutrition">';

    if ( $lignes ) {
        echo '<p class="pepe-nutrition__titre">Valeurs nutritionnelles <span class="pepe-nutrition__portion">pour une portion</span></p>';
        echo '<ul class="pepe-nutrition__liste">';
        foreach ( $lignes as $ligne ) {
            echo '<li class="pepe-nutrition__item">';
            echo '<span class="pepe-nutrition__valeur">' . esc_html( $ligne['valeur'] ) . '</span>';
            echo '<span class="pepe-nutrition__nom">' . esc_html( $ligne['nom'] ) . '</span>';
            echo '</li>';
        }
        echo '</ul>';
    }

    if ( $allergenes ) {
        echo '<p class="pepe-nutrition__allergenes">';
        echo '<strong>Allergènes :</strong> ' . esc_html( implode( ', ', $allergenes ) );
        echo '</p>';
    }

    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'pepe_afficher_nutrition', 7 );
