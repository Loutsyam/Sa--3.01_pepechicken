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

        $lignes[ $nom ] = array(
            'nom'    => $champ['label'],
            'valeur' => number_format_i18n( $valeur, $decimales ) . ' ' . $unite,
        );
    }

    $allergenes = get_field( 'pepe_allergenes' );

    if ( empty( $lignes ) && empty( $allergenes ) ) {
        return; // rien à afficher pour ce produit
    }

    // Même HTML que le tableau « Informations complémentaires » de WooCommerce
    // (woocommerce-product-attributes / shop_attributes, une ligne --nom par valeur),
    // affiché en petites cartes en CSS
    echo '<table class="woocommerce-product-attributes shop_attributes">';
    if ( $lignes ) {
        echo '<caption>Valeurs nutritionnelles <small>pour une portion</small></caption>';
    }

    foreach ( $lignes as $nom => $ligne ) {
        echo '<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--' . esc_attr( $nom ) . '">';
        echo '<th class="woocommerce-product-attributes-item__label" scope="row">' . esc_html( $ligne['nom'] ) . '</th>';
        echo '<td class="woocommerce-product-attributes-item__value">' . esc_html( $ligne['valeur'] ) . '</td>';
        echo '</tr>';
    }

    if ( $allergenes ) {
        echo '<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--allergenes">';
        echo '<th class="woocommerce-product-attributes-item__label" scope="row">Allergènes :</th>';
        echo '<td class="woocommerce-product-attributes-item__value">' . esc_html( implode( ', ', $allergenes ) ) . '</td>';
        echo '</tr>';
    }

    echo '</table>';
}
add_action( 'woocommerce_single_product_summary', 'pepe_afficher_nutrition', 7 );
