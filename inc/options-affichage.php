<?php
/**
 * IT-3 – US-3.2 : sélecteur d'options sur la fiche produit
 *
 * Les options sont des champs « Case à cocher » créés dans SCF
 * (groupe « Options du produit », enregistré dans acf-json/).
 * Sur chaque produit, on coche les choix proposés au client.
 *
 * Sur la fiche, chaque champ est un menu repliable (<details>) avec
 * des pilules « pepe-option » (Design System, 07 Badges et options).
 * Repliés, les menus prennent peu de place : le bouton « Ajouter au panier »
 * reste visible sans faire défiler la page (ligne de flottaison).
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

/**
 * Le produit est-il variable avec un attribut « Formule » (Burger seul / Menu) ?
 * (US-3.1 : la formule est une vraie variation WooCommerce, avec son prix et son stock)
 */
function pepe_a_formule_variable( $produit ) {
    if ( ! $produit || ! $produit->is_type( 'variable' ) ) {
        return false;
    }

    foreach ( array_keys( $produit->get_variation_attributes() ) as $attribut ) {
        if ( 'formule' === sanitize_title( $attribut ) ) {
            return true;
        }
    }

    return false;
}

/* Affiche les options juste avant le bouton « Ajouter au panier » */
function pepe_afficher_options() {
    global $product;

    $options = pepe_options_du_produit( $product->get_id() );

    if ( empty( $options ) ) {
        return;
    }

    // Accompagnement et boisson ne sont cachés que si le produit a une formule
    // (pilules SCF « Formule » ou variations Burger seul / Menu)
    $a_une_formule = isset( $options['pepe_formule'] ) || pepe_a_formule_variable( $product );

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

        // Options repliables : seule la formule est ouverte au départ,
        // le titre montre le choix fait (rempli en JavaScript)
        $ouvert = ( 'pepe_formule' === $nom ) ? ' open' : '';

        echo '<details class="' . esc_attr( $classe ) . '"' . $ouvert . '>';

        echo '<summary class="pepe-choix__titre">' . esc_html( $groupe['titre'] );
        if ( $groupe['note'] ) {
            echo ' <span class="pepe-choix__note">' . esc_html( $groupe['note'] ) . '</span>';
        }
        echo ' <span class="pepe-choix__resume"></span>';
        echo '</summary>';

        echo '<fieldset class="pepe-choix__groupe">';
        echo '<legend class="screen-reader-text">' . esc_html( $groupe['titre'] ) . '</legend>';

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
        echo '</details>';
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

/*
 * Variations (US-3.1) : WooCommerce affiche une liste déroulante par attribut.
 * On la garde (son script s'en sert pour trouver la variation, son prix et
 * son stock) mais on la cache, et on affiche les mêmes pilules que les options.
 */
function pepe_classe_liste_variation( $args ) {
    $args['class'] = 'pepe-options__select';
    return $args;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'pepe_classe_liste_variation' );

function pepe_pilules_variation( $html, $args ) {
    if ( empty( $args['options'] ) ) {
        return $html;
    }

    $pilules = '<div class="pepe-options" role="group" aria-label="' . esc_attr( wc_attribute_label( $args['attribute'], $args['product'] ) ) . '">';

    foreach ( $args['options'] as $option ) {
        $pilules .= '<button type="button" class="pepe-option pepe-option--variation" data-valeur="' . esc_attr( $option ) . '" aria-pressed="false">' . esc_html( $option ) . '</button>';
    }

    $pilules .= '</div>';

    return $html . $pilules;
}
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'pepe_pilules_variation', 10, 2 );
