<?php
/**
 * Thème enfant Pepe Chicken (Storefront)
 */

// Styles : Storefront puis thème enfant
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'storefront-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'pepe-chicken-style', get_stylesheet_uri(), array( 'storefront-style' ), wp_get_theme()->get( 'Version' ) );
} );

// Header et footer personnalisés (remplacent ceux de Storefront)
add_action( 'init', function () {
    remove_all_actions( 'storefront_header' );
    remove_all_actions( 'storefront_footer' );
    add_action( 'storefront_header', 'pepe_header' );
    add_action( 'storefront_footer', 'pepe_footer' );

    // Pas de barre latérale (la maquette n'en a pas)
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
} );

// Menu de navigation du header (à remplir dans Apparence > Menus)
add_action( 'after_setup_theme', function () {
    register_nav_menus( array( 'pepe-header' => 'Menu header Pepe Chicken' ) );
} );

function pepe_header() {
    $boutique = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    $panier   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
    $nombre   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
    ?>
    <div class="pepe-header">
        <nav class="pepe-nav" aria-label="Menu principal">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'pepe-header',
                'container'      => false,
                'fallback_cb'    => 'pepe_menu_par_defaut',
                'depth'          => 1,
            ) );
            ?>
        </nav>

        <a class="pepe-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php if ( has_custom_logo() ) : ?>
                <?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full' ); ?>
            <?php else : ?>
                <span class="pepe-logo-pepe">PEPE</span>
                <span class="pepe-logo-chicken">Chicken®</span>
            <?php endif; ?>
        </a>

        <div class="pepe-actions">
            <a class="pepe-btn pepe-btn--rouge" href="<?php echo esc_url( $boutique ); ?>">Commander</a>
            <a class="pepe-panier" href="<?php echo esc_url( $panier ); ?>">
                🛒 Panier <span class="pepe-panier-nombre"><?php echo (int) $nombre; ?></span>
            </a>
        </div>
    </div>
    <?php
}

function pepe_menu_par_defaut() {
    $boutique = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Le concept</a></li>';
    echo '<li><a href="' . esc_url( $boutique ) . '">Le menu</a></li>';
    echo '<li><a href="#">Où nous trouver</a></li>';
    echo '</ul>';
}

// Met à jour le nombre d'articles du panier sans recharger la page
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
    $fragments['span.pepe-panier-nombre'] = '<span class="pepe-panier-nombre">' . (int) WC()->cart->get_cart_contents_count() . '</span>';
    return $fragments;
} );

function pepe_footer() {
    ?>
    <div class="pepe-footer">
        <div class="pepe-footer-colonnes">
            <div>
                <h3>Accès rapide</h3>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Le concept</a></li>
                    <li><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>">Le menu</a></li>
                    <li><a href="#">Où nous trouver</a></li>
                    <li><a href="#">Le shop</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h3>Services consommateurs</h3>
                <ul>
                    <li><a href="#">Informations allergènes</a></li>
                    <li><a href="#">Origine des viandes</a></li>
                </ul>
            </div>
            <div class="pepe-footer-logo">
                <span class="pepe-logo-pepe">PEPE</span>
                <span class="pepe-logo-chicken">Chicken®</span>
            </div>
            <div>
                <h3>Contact</h3>
                <ul>
                    <li><a href="#">Nous contacter</a></li>
                </ul>
            </div>
            <div>
                <h3>Informations légales</h3>
                <ul>
                    <li><a href="#">Mentions légales</a></li>
                    <li><a href="#">CGV</a></li>
                    <li><a href="#">Politique de confidentialité</a></li>
                </ul>
            </div>
        </div>
        <p class="pepe-footer-sante">Pour votre santé, pratiquez une activité physique régulière : <a href="https://www.mangerbouger.fr" target="_blank" rel="noopener">www.mangerbouger.fr</a></p>
    </div>
    <?php
}

/* ---------- WooCommerce ---------- */

// 3 produits par ligne
add_filter( 'loop_shop_columns', function () { return 3; } );

// Titre de la page Boutique
add_filter( 'woocommerce_page_title', function ( $titre ) {
    return is_shop() ? 'Découvre toute la carte<br>Pepe Chicken' : $titre;
} );

// Bouton d'ajout rapide "+" dans les grilles de produits
add_filter( 'woocommerce_product_add_to_cart_text', function ( $texte, $produit ) {
    if ( is_product() ) {
        return $texte;
    }
    return $produit->is_type( 'simple' ) && $produit->is_purchasable() && $produit->is_in_stock() ? '+' : '→';
}, 10, 2 );

// Onglets de catégories sous le titre de la Boutique
add_action( 'woocommerce_archive_description', function () {
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
    ) );
    if ( empty( $categories ) || is_wp_error( $categories ) ) {
        return;
    }
    $courante = is_product_category() ? get_queried_object_id() : 0;
    echo '<nav class="pepe-categories" aria-label="Catégories">';
    echo '<a class="' . ( $courante ? '' : 'is-active' ) . '" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Tout</a>';
    foreach ( $categories as $categorie ) {
        $classe = ( $courante === $categorie->term_id ) ? 'is-active' : '';
        echo '<a class="' . $classe . '" href="' . esc_url( get_term_link( $categorie ) ) . '">' . esc_html( $categorie->name ) . '</a>';
    }
    echo '</nav>';
}, 5 );

// Page Boutique : produits regroupés par catégorie (comme la maquette)
function pepe_boutique_par_categorie() {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
    ) );
    foreach ( $categories as $categorie ) {
        echo '<section class="pepe-section" id="' . esc_attr( $categorie->slug ) . '">';
        echo '<h2 class="pepe-section-titre">' . esc_html( $categorie->name ) . '</h2>';
        echo do_shortcode( '[products category="' . esc_attr( $categorie->slug ) . '" columns="3" limit="12"]' );
        echo '</section>';
    }
}
