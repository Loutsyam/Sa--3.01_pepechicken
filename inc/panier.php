<?php
/**
 * IT-5 : page Panier (US-5.3) et choix du mode d'expédition (US-5.4)
 *
 * Les templates sont dans woocommerce/cart/ (cart.php, cart-totals.php,
 * proceed-to-checkout-button.php). Ici : les petites fonctions qu'ils utilisent.
 */

defined( 'ABSPATH' ) || exit;

/* Titre de la page (titre de Storefront, entry-title) : « Mon panier » + nombre d'articles */
function pepe_titre_panier( $titre, $id = 0 ) {
    if ( is_cart() && in_the_loop() && wc_get_page_id( 'cart' ) === $id && WC()->cart && ! WC()->cart->is_empty() ) {
        $nombre = WC()->cart->get_cart_contents_count();
        return 'Mon panier <small>' . $nombre . ( $nombre > 1 ? ' articles' : ' article' ) . '</small>';
    }
    return $titre;
}
add_filter( 'the_title', 'pepe_titre_panier', 10, 2 );

/*
 * Étapes de la commande (panier, retrait et paiement, confirmation) sous le titre :
 * même HTML que le fil d'Ariane de WooCommerce (woocommerce-breadcrumb, breadcrumb-separator)
 */
function pepe_etapes_commande( $etape ) {
    $etapes = array( 1 => '1 · Panier', 2 => '2 · Retrait &amp; paiement', 3 => '3 · Confirmation' );

    echo '<nav class="woocommerce-breadcrumb" aria-label="Étapes de la commande">';
    foreach ( $etapes as $numero => $texte ) {
        if ( $numero > 1 ) {
            echo '<span class="breadcrumb-separator"> › </span>';
        }
        if ( $numero === $etape ) {
            echo '<strong aria-current="step">' . $texte . '</strong>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo '<span>' . $texte . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
    echo '</nav>';
}

function pepe_etapes_panier() {
    pepe_etapes_commande( 1 );
}
add_action( 'woocommerce_before_cart', 'pepe_etapes_panier', 5 );

/* « Un petit plus ? » est affiché sous les articles (à la place des ventes croisées de la colonne de droite) */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_before_cart_collaterals', 'pepe_suggestions_panier' );

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

    echo '<h3>Mode de récupération</h3>';

    foreach ( $paquets as $index => $paquet ) {
        $methodes = $paquet['rates'];

        if ( empty( $methodes ) ) {
            echo '<p class="woocommerce-shipping-contents">Aucun mode de récupération disponible pour cette adresse.</p>';
            continue;
        }

        $choisi = isset( $choisis[ $index ] ) && isset( $methodes[ $choisis[ $index ] ] ) ? $choisis[ $index ] : key( $methodes );

        // Même liste que cart/cart-shipping.php de WooCommerce (#shipping_method, woocommerce-shipping-methods)
        echo '<ul id="shipping_method" class="woocommerce-shipping-methods">';
        foreach ( $methodes as $methode ) {
            $id_champ = 'shipping_method_' . $index . '_' . sanitize_title( $methode->get_id() );
            $prix     = (float) $methode->get_cost() > 0 ? wp_strip_all_tags( wc_price( $methode->get_cost() ) ) : 'Gratuit';
            $retrait  = 'local_pickup' === $methode->get_method_id();
            $detail   = $retrait ? 'Retrait en restaurant · ' . $prix : 'À domicile · ' . $prix;

            echo '<li>';
            echo '<input type="radio" class="shipping_method" name="shipping_method[' . absint( $index ) . ']" data-index="' . absint( $index ) . '" id="' . esc_attr( $id_champ ) . '" value="' . esc_attr( $methode->get_id() ) . '" ' . checked( $methode->get_id(), $choisi, false ) . '>';
            echo '<label for="' . esc_attr( $id_champ ) . '">' . esc_html( $methode->get_label() ) . '<small>' . esc_html( $detail ) . '</small></label>';
            echo '</li>';
        }
        echo '</ul>';
    }
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

    // Même HTML que les ventes croisées (cross-sells) et la liste du widget produits de WooCommerce
    echo '<div class="cross-sells">';
    echo '<h2>Un petit plus ?</h2>';
    echo '<ul class="product_list_widget">';

    foreach ( $ids as $id ) {
        $produit = wc_get_product( $id );
        if ( ! $produit || ! $produit->is_purchasable() ) {
            continue;
        }

        echo '<li>';
        echo '<a href="' . esc_url( $produit->get_permalink() ) . '">';
        echo $produit->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<span class="product-title">' . esc_html( $produit->get_name() ) . '</span>';
        echo '</a>';
        echo wp_kses_post( $produit->get_price_html() );
        echo '<a class="button add_to_cart_button" href="' . esc_url( $produit->add_to_cart_url() ) . '" aria-label="' . esc_attr( 'Ajouter ' . $produit->get_name() . ' au panier' ) . '">+</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
}
