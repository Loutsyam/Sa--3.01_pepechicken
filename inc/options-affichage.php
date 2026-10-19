<?php
/**
 * IT-3 – US-3.2 : sélecteur d'options sur la fiche produit
 *
 * Les options sont des champs « Case à cocher » créés dans SCF
 * (groupe « Options du produit », enregistré dans acf-json/).
 * Sur chaque produit, on coche les choix proposés au client.
 *
 * Sur la fiche, chaque champ est affiché dans un <fieldset> avec
 * des pilules « pepe-option » (Design System, 07 Badges et options).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Supplément écrit dans le libellé du choix dans SCF.
 * « Mac'n Cheese Balls +1,00 € » => 1.00 / « Tater Tots » => 0
 */
function pepe_prix_du_libelle( $libelle ) {
    if ( preg_match( '/\+\s*(\d+),(\d{2})/', $libelle, $morceaux ) ) {
        return (float) ( $morceaux[1] . '.' . $morceaux[2] );
    }

    return 0;
}

/**
 * Options d'un produit, lues dans ses champs SCF.
 * Un champ sans aucune case cochée n'est pas affiché.
 */
function pepe_options_du_produit( $produit_id ) {
    $options = array();

    if ( ! function_exists( 'get_field_object' ) ) {
        return $options; // SCF n'est pas activé
    }

    // Champs du groupe SCF, dans l'ordre de la maquette
    $champs = array( 'pepe_formule', 'pepe_accompagnement', 'pepe_boisson', 'pepe_sauce', 'pepe_piquant', 'pepe_retirer', 'pepe_supplements' );

    // Le client peut cocher plusieurs choix (sinon un seul, obligatoire)
    $plusieurs_choix = array( 'pepe_retirer', 'pepe_supplements' );

    // Affichés seulement avec la formule Menu
    $seulement_menu = array( 'pepe_accompagnement', 'pepe_boisson' );

    foreach ( $champs as $nom ) {
        $champ = get_field_object( $nom, $produit_id );

        if ( ! $champ || empty( $champ['value'] ) ) {
            continue;
        }

        $choix = array();
        foreach ( $champ['value'] as $coche ) {
            $choix[ $coche['value'] ] = array(
                'texte' => $coche['label'],
                'prix'  => pepe_prix_du_libelle( $coche['label'] ),
            );
        }

        $options[ $nom ] = array(
            'titre' => $champ['label'],
            'note'  => $champ['instructions'],
            'type'  => in_array( $nom, $plusieurs_choix, true ) ? 'checkbox' : 'radio',
            'menu'  => in_array( $nom, $seulement_menu, true ),
            'choix' => $choix,
        );
    }

    return $options;
}

/* Affiche les options juste avant le bouton « Ajouter au panier » */
function pepe_afficher_options() {
    global $product;

    $options = pepe_options_du_produit( $product->get_id() );

    if ( empty( $options ) ) {
        return;
    }

    // Accompagnement et boisson ne sont cachés que si le produit a une formule
    $a_une_formule = isset( $options['pepe_formule'] );

    // Données pour le calcul du prix en JavaScript (US-3.3)
    echo '<div class="pepe-choix-liste"';
    echo ' data-prix="' . esc_attr( wc_get_price_to_display( $product ) ) . '"';
    echo ' data-decimales="' . esc_attr( wc_get_price_decimals() ) . '"';
    echo ' data-separateur="' . esc_attr( wc_get_price_decimal_separator() ) . '"';
    echo ' data-devise="' . esc_attr( html_entity_decode( get_woocommerce_currency_symbol() ) ) . '">';

    foreach ( $options as $nom => $groupe ) {
        $classe = 'pepe-choix';
        if ( $groupe['menu'] && $a_une_formule ) {
            $classe .= ' pepe-choix--menu';
        }

        echo '<fieldset class="' . esc_attr( $classe ) . '">';

        echo '<legend class="pepe-choix__titre">' . esc_html( $groupe['titre'] );
        if ( $groupe['note'] ) {
            echo ' <span class="pepe-choix__note">' . esc_html( $groupe['note'] ) . '</span>';
        }
        echo '</legend>';

        echo '<div class="pepe-options">';

        // Radio : pepe_sauce / Cases à cocher : pepe_retirer[]
        $name = ( 'checkbox' === $groupe['type'] ) ? $nom . '[]' : $nom;

        foreach ( $groupe['choix'] as $valeur => $choix ) {
            echo '<label class="pepe-option">';
            echo '<input class="pepe-option__input" type="' . esc_attr( $groupe['type'] ) . '"';
            echo ' name="' . esc_attr( $name ) . '" value="' . esc_attr( $valeur ) . '"';
            echo ' data-prix="' . esc_attr( $choix['prix'] ) . '"';
            if ( 'pepe_formule' === $nom && 'Menu' === $valeur ) {
                echo ' data-menu="1"';
            }
            echo '>';
            echo '<span class="pepe-option__texte">' . esc_html( $choix['texte'] ) . '</span>';
            echo '</label>';
        }

        echo '</div>';
        echo '</fieldset>';
    }

    echo '</div>';
}
add_action( 'woocommerce_before_add_to_cart_button', 'pepe_afficher_options' );

/**
 * Dans la boutique, le bouton « + » d'un produit avec options
 * ouvre la fiche produit (il faut d'abord choisir ses options).
 */
function pepe_lien_bouton_boutique( $url, $produit ) {
    if ( pepe_options_du_produit( $produit->get_id() ) ) {
        return $produit->get_permalink();
    }

    return $url;
}
add_filter( 'woocommerce_product_add_to_cart_url', 'pepe_lien_bouton_boutique', 10, 2 );

function pepe_pas_d_ajax_si_options( $supporte, $fonction, $produit ) {
    if ( 'ajax_add_to_cart' === $fonction && pepe_options_du_produit( $produit->get_id() ) ) {
        return false;
    }

    return $supporte;
}
add_filter( 'woocommerce_product_supports', 'pepe_pas_d_ajax_si_options', 10, 3 );
