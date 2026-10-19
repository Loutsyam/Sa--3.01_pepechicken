<?php
/**
 * IT-3 – US-3.3 : prix selon les options, panier et commande
 *
 * 1. Fiche produit : le prix change quand on choisit une option (js/options-prix.js)
 * 2. Ajout au panier : on vérifie les choix obligatoires
 * 3. Panier : on garde les choix et on ajoute les suppléments au prix
 * 4. Commande : les choix sont enregistrés (visibles par le restaurant)
 */

defined( 'ABSPATH' ) || exit;

/* 1. Le script ne se charge que sur les fiches produit */
function pepe_script_prix() {
    if ( is_product() ) {
        wp_enqueue_script(
            'pepe-options-prix',
            get_stylesheet_directory_uri() . '/js/options-prix.js',
            array( 'jquery' ),
            '1.1.0',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'pepe_script_prix' );

/**
 * Choix envoyés par le formulaire. On ne garde que les choix
 * cochés dans SCF pour ce produit (sécurité).
 * Renvoie : 'pepe_sauce' => array( 'Creamy BBQ' ), …
 */
function pepe_choix_envoyes( $produit_id ) {
    $options = pepe_options_du_produit( $produit_id );
    $choix = array();

    // Formule Menu choisie ? (sinon accompagnement et boisson ne comptent pas)
    // Pilules SCF : champ pepe_formule / Variation (US-3.1) : champ attribute_formule
    if ( isset( $options['pepe_formule'] ) ) {
        $formule  = isset( $_POST['pepe_formule'] ) ? sanitize_text_field( wp_unslash( $_POST['pepe_formule'] ) ) : '';
        $est_menu = 'Menu' === $formule;
    } elseif ( isset( $_POST['attribute_formule'] ) ) {
        $est_menu = 'Menu' === sanitize_text_field( wp_unslash( $_POST['attribute_formule'] ) );
    } else {
        $est_menu = true;
    }

    foreach ( $options as $nom => $groupe ) {
        if ( $groupe['menu'] && ! $est_menu ) {
            continue;
        }

        $valeurs = isset( $_POST[ $nom ] ) ? (array) wp_unslash( $_POST[ $nom ] ) : array();
        $valeurs = array_map( 'sanitize_text_field', $valeurs );

        // On ne garde que les choix qui existent pour ce produit
        $choix[ $nom ] = array_values( array_intersect( $valeurs, array_keys( $groupe['choix'] ) ) );
    }

    return $choix;
}

/* 2. Vérification : un choix obligatoire pour chaque groupe « un seul choix » affiché */
function pepe_valider_choix( $valide, $produit_id ) {
    $options = pepe_options_du_produit( $produit_id );

    foreach ( pepe_choix_envoyes( $produit_id ) as $nom => $valeurs ) {
        if ( 'radio' === $options[ $nom ]['type'] && empty( $valeurs ) ) {
            wc_add_notice( 'Choisis une option pour : ' . $options[ $nom ]['titre'] . '.', 'error' );
            $valide = false;
        }
    }

    return $valide;
}
add_filter( 'woocommerce_add_to_cart_validation', 'pepe_valider_choix', 10, 2 );

/* 3a. On garde les choix (« Sauce » => « Creamy BBQ ») et le total des suppléments */
function pepe_ajouter_choix_panier( $donnees, $produit_id ) {
    $options = pepe_options_du_produit( $produit_id );

    if ( empty( $options ) ) {
        return $donnees;
    }

    $lignes = array();
    $supplement = 0;

    foreach ( array_filter( pepe_choix_envoyes( $produit_id ) ) as $nom => $valeurs ) {
        foreach ( $valeurs as $valeur ) {
            $supplement = $supplement + $options[ $nom ]['choix'][ $valeur ]['prix'];
        }

        $lignes[ $options[ $nom ]['titre'] ] = implode( ', ', $valeurs );
    }

    $donnees['pepe_choix'] = $lignes;
    $donnees['pepe_supplement'] = $supplement;

    return $donnees;
}
add_filter( 'woocommerce_add_cart_item_data', 'pepe_ajouter_choix_panier', 10, 2 );

/* 3b. Prix du produit dans le panier = prix normal + suppléments */
function pepe_prix_panier( $article ) {
    if ( ! empty( $article['pepe_supplement'] ) ) {
        $prix = (float) $article['data']->get_price( 'edit' );
        $article['data']->set_price( $prix + $article['pepe_supplement'] );
    }

    return $article;
}
add_filter( 'woocommerce_add_cart_item', 'pepe_prix_panier' );
add_filter( 'woocommerce_get_cart_item_from_session', 'pepe_prix_panier' );

/* 3c. Les choix affichés sous le nom du produit (panier, validation de commande) */
function pepe_afficher_choix_panier( $lignes, $article ) {
    if ( ! empty( $article['pepe_choix'] ) ) {
        foreach ( $article['pepe_choix'] as $titre => $valeur ) {
            $lignes[] = array(
                'key'   => $titre,
                'value' => $valeur,
            );
        }
    }

    return $lignes;
}
add_filter( 'woocommerce_get_item_data', 'pepe_afficher_choix_panier', 10, 2 );

/* 4. Les choix sont enregistrés dans la commande */
function pepe_enregistrer_choix_commande( $ligne, $cle_panier, $article ) {
    if ( ! empty( $article['pepe_choix'] ) ) {
        foreach ( $article['pepe_choix'] as $titre => $valeur ) {
            $ligne->add_meta_data( $titre, $valeur );
        }
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'pepe_enregistrer_choix_commande', 10, 3 );
