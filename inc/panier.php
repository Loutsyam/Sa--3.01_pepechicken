<?php
/**
 * IT-5 : page Panier (US-5.3) et choix du mode d'expédition (US-5.4)
 *
 * Les templates sont dans woocommerce/cart/ (cart.php, cart-totals.php,
 * proceed-to-checkout-button.php). Ici : les petites fonctions qu'ils utilisent.
 */

defined( 'ABSPATH' ) || exit;

/* Le titre « Panier » de la page est remplacé par celui du template (« Mon panier ») */
function pepe_cacher_titre_panier( $titre, $id = 0 ) {
    if ( is_cart() && in_the_loop() && wc_get_page_id( 'cart' ) === $id && WC()->cart && ! WC()->cart->is_empty() ) {
        return '';
    }
    return $titre;
}
add_filter( 'the_title', 'pepe_cacher_titre_panier', 10, 2 );

/* « Un petit plus ? » est affiché sous les articles : on l'enlève de la colonne de droite */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/* Script des boutons − / + de quantité */
function pepe_script_panier() {
    if ( is_cart() ) {
        wp_enqueue_script(
            'pepe-panier',
            get_stylesheet_directory_uri() . '/js/panier.js',
            array( 'jquery', 'wc-cart' ),
            '1.0.0',
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'pepe_script_panier' );

/**
 * Options choisies d'un article, sur une ligne : « Menu · Creamy BBQ · Medium »
 * (les choix sont enregistrés dans le panier par inc/options-prix.php).
 */
function pepe_details_article_panier( $article ) {
    $details = array();

    // Variation choisie (US-3.1) : « Menu » ou « Burger seul »
    if ( ! empty( $article['variation'] ) ) {
        foreach ( $article['variation'] as $valeur ) {
            $details[] = $valeur;
        }
    }

    if ( ! empty( $article['pepe_choix'] ) ) {
        $details = array_merge( $details, array_values( $article['pepe_choix'] ) );
    }

    return implode( ' · ', $details );
}

/**
 * Mode de récupération (US-5.4) : les modes d'expédition de la zone
 * (Retrait en boutique, Livraison…) en cartes à bouton radio.
 * Les champs gardent le nom et la classe attendus par WooCommerce
 * (shipping_method[0], .shipping_method) : le total se met à jour tout seul.
 */
function pepe_choix_expedition() {
    $paquets = WC()->shipping()->get_packages();
    $choisis = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods' ) : array();

    echo '<fieldset class="pepe-recap__bloc pepe-expedition">';
    echo '<legend class="pepe-recap__sous-titre">Mode de récupération</legend>';

    foreach ( $paquets as $index => $paquet ) {
        $methodes = $paquet['rates'];

        if ( empty( $methodes ) ) {
            echo '<p class="pepe-expedition__vide">Aucun mode de récupération disponible pour cette adresse.</p>';
            continue;
        }

        $choisi = isset( $choisis[ $index ] ) && isset( $methodes[ $choisis[ $index ] ] ) ? $choisis[ $index ] : key( $methodes );

        foreach ( $methodes as $methode ) {
            $id_champ = 'shipping_method_' . $index . '_' . sanitize_title( $methode->get_id() );
            $prix     = (float) $methode->get_cost() > 0 ? wp_strip_all_tags( wc_price( $methode->get_cost() ) ) : 'Gratuit';
            $retrait  = 'local_pickup' === $methode->get_method_id();
            $detail   = $retrait ? 'Retrait en restaurant · ' . $prix : 'À domicile · ' . $prix;

            echo '<label class="pepe-expedition__choix" for="' . esc_attr( $id_champ ) . '">';
            echo '<input type="radio" class="shipping_method pepe-expedition__radio" name="shipping_method[' . absint( $index ) . ']" data-index="' . absint( $index ) . '" id="' . esc_attr( $id_champ ) . '" value="' . esc_attr( $methode->get_id() ) . '" ' . checked( $methode->get_id(), $choisi, false ) . '>';
            echo '<span class="pepe-expedition__texte">';
            echo '<span class="pepe-expedition__nom">' . esc_html( $methode->get_label() ) . '</span>';
            echo '<span class="pepe-expedition__detail">' . esc_html( $detail ) . '</span>';
            echo '</span>';
            echo '</label>';
        }
    }

    echo '</fieldset>';
}

/**
 * « Un petit plus ? » : 3 produits à ajouter (ventes croisées du panier,
 * sinon des sauces et des accompagnements qui ne sont pas déjà dans le panier).
 * IT-13 : les ventes croisées réglées dans l'admin passent en premier.
 */
function pepe_suggestions_panier() {
    $dans_le_panier = array();
    foreach ( WC()->cart->get_cart() as $article ) {
        $dans_le_panier[] = $article['product_id'];
    }

    $ids = array_diff( WC()->cart->get_cross_sells(), $dans_le_panier );

    if ( count( $ids ) < 3 ) {
        $categories = array();
        foreach ( array( 'Sauces', 'Sides', 'Poulets frit' ) as $nom ) {
            $terme = get_term_by( 'name', $nom, 'product_cat' );
            if ( $terme ) {
                $categories[] = $terme->slug;
            }
        }

        if ( $categories ) {
            $autres = wc_get_products( array(
                'status'   => 'publish',
                'category' => $categories,
                'exclude'  => array_merge( $dans_le_panier, $ids ),
                'limit'    => 3 - count( $ids ),
                'orderby'  => 'rand',
                'return'   => 'ids',
            ) );
            $ids = array_merge( $ids, $autres );
        }
    }

    $ids = array_slice( $ids, 0, 3 );

    if ( empty( $ids ) ) {
        return;
    }

    echo '<section class="pepe-suggestions">';
    echo '<h2 class="pepe-suggestions__titre">Un petit plus ?</h2>';
    echo '<ul class="pepe-suggestions__liste">';

    foreach ( $ids as $id ) {
        $produit = wc_get_product( $id );
        if ( ! $produit || ! $produit->is_purchasable() ) {
            continue;
        }

        echo '<li class="pepe-suggestion">';
        echo '<span class="pepe-suggestion__image">' . $produit->get_image( 'woocommerce_gallery_thumbnail' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span class="pepe-suggestion__infos">';
        echo '<a class="pepe-suggestion__nom" href="' . esc_url( $produit->get_permalink() ) . '">' . esc_html( $produit->get_name() ) . '</a>';
        echo '<span class="pepe-suggestion__prix">' . wp_kses_post( $produit->get_price_html() ) . '</span>';
        echo '</span>';
        echo '<a class="pepe-suggestion__ajouter" href="' . esc_url( $produit->add_to_cart_url() ) . '" aria-label="' . esc_attr( 'Ajouter ' . $produit->get_name() . ' au panier' ) . '">+</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</section>';
}
