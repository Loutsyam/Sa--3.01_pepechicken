<?php
/**
 * Thème enfant Pepe Chicken (parent : Storefront)
 *
 * Principe : on garde la structure et les fonctionnalités de Storefront
 * (menu mobile, mini-panier, liens d'accessibilité, widgets) et on la
 * complète avec nos propres éléments, nommés en BEM avec le préfixe « pepe- ».
 *
 * La feuille de style du thème enfant est chargée automatiquement par
 * Storefront (handle « storefront-child-style »), pas besoin de l'ajouter ici.
 */

defined( 'ABSPATH' ) || exit;

/* Réglages des hooks Storefront */
add_action( 'init', function () {
    // Header : on retire la recherche et le menu secondaire (absents de la maquette)
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );

    // Panier Storefront déplacé dans notre bloc d'actions (avec le bouton Commander)
    remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    add_action( 'storefront_header', 'pepe_header_actions', 60 );

    // Footer : les zones de widgets Storefront restent, on remplace seulement les crédits
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'pepe_footer', 20 );

    // Pas de barre latérale (la maquette n'en a pas)
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
} );

/* Header */

/**
 * Logo : remplace celui de Storefront (fonction « pluggable »).
 * Si un logo est défini dans Apparence > Personnaliser, il est utilisé.
 */
function storefront_site_branding() {
    ?>
    <div class="site-branding pepe-logo">
        <?php if ( has_custom_logo() ) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <a class="pepe-logo__lien" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <span class="pepe-logo__pepe">PEPE</span>
                <span class="pepe-logo__chicken">Chicken®</span>
                <span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
            </a>
        <?php endif; ?>
    </div>
    <?php
}

/** Bouton Commander + panier Storefront (mini-panier et mise à jour AJAX inclus). */
function pepe_header_actions() {
    $boutique = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    ?>
    <div class="pepe-header__actions">
        <a class="pepe-btn pepe-btn--rouge pepe-header__commander" href="<?php echo esc_url( $boutique ); ?>">Commander</a>
        <?php
        if ( function_exists( 'storefront_header_cart' ) ) {
            storefront_header_cart();
        }
        ?>
    </div>
    <?php
}

/**
 * Menu par défaut tant qu'aucun menu n'est attribué aux emplacements
 * Storefront « Primaire » et « Mobile » (Apparence > Menus).
 */
add_filter( 'wp_nav_menu_args', function ( $args ) {
    if ( in_array( $args['theme_location'], array( 'primary', 'handheld' ), true ) && ! has_nav_menu( $args['theme_location'] ) ) {
        $args['fallback_cb'] = 'pepe_menu_par_defaut';
    }
    return $args;
} );

function pepe_menu_par_defaut( $args ) {
    $boutique = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    $classe   = ! empty( $args['container_class'] ) ? $args['container_class'] : 'menu';
    echo '<div class="' . esc_attr( $classe ) . '"><ul class="menu">';
    echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">Le concept</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url( $boutique ) . '">Le menu</a></li>';
    echo '<li class="menu-item"><a href="#">Où nous trouver</a></li>';
    echo '</ul></div>';
}

/* Footer */

/** URL d'une page à partir de son identifiant de lien (slug), « # » si elle n'existe pas encore. */
function pepe_url_page( $slug ) {
    $page = get_page_by_path( $slug );
    return $page ? get_permalink( $page ) : '#';
}

function pepe_footer() {
    $boutique = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
    $cgv      = function_exists( 'wc_terms_and_conditions_page_id' ) && wc_terms_and_conditions_page_id() ? get_permalink( wc_terms_and_conditions_page_id() ) : pepe_url_page( 'cgv' );
    $vie_priv = get_privacy_policy_url() ? get_privacy_policy_url() : '#';

    $colonnes = array(
        'Accès rapide'           => array(
            'Le concept'      => home_url( '/' ),
            'Le menu'         => $boutique,
            'Où nous trouver' => '#',
            'FAQ'             => pepe_url_page( 'faq' ),
        ),
        'Services consommateurs' => array(
            'Informations allergènes' => pepe_url_page( 'allergenes' ),
            'Origine des viandes'     => pepe_url_page( 'origine-des-viandes' ),
        ),
        'Contact'                => array(
            'Nous contacter' => pepe_url_page( 'contact' ),
        ),
        'Informations légales'   => array(
            'Mentions légales'             => pepe_url_page( 'mentions-legales' ),
            'CGV'                          => $cgv,
            'Politique de confidentialité' => $vie_priv,
        ),
    );
    ?>
    <div class="pepe-footer">
        <div class="pepe-footer__colonnes">
            <?php
            $i = 0;
            foreach ( $colonnes as $titre => $liens ) :
                if ( 2 === $i ) : // logo au centre, comme la maquette
                    ?>
                    <div class="pepe-footer__logo pepe-logo">
                        <span class="pepe-logo__pepe">PEPE</span>
                        <span class="pepe-logo__chicken">Chicken®</span>
                    </div>
                    <?php
                endif;
                ?>
                <div class="pepe-footer__colonne">
                    <h2 class="pepe-footer__titre"><?php echo esc_html( $titre ); ?></h2>
                    <ul class="pepe-footer__liste">
                        <?php foreach ( $liens as $texte => $url ) : ?>
                            <li class="pepe-footer__element"><a class="pepe-footer__lien" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $texte ); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
                $i++;
            endforeach;
            ?>
        </div>
        <p class="pepe-footer__sante">Pour votre santé, pratiquez une activité physique régulière : <a class="pepe-footer__lien" href="https://www.mangerbouger.fr" target="_blank" rel="noopener">www.mangerbouger.fr</a></p>
    </div>
    <?php
}

/* WooCommerce */

// 3 produits par ligne
add_filter( 'loop_shop_columns', function () {
    return 3;
} );

// Titre de la page Boutique
add_filter( 'woocommerce_page_title', function ( $titre ) {
    return is_shop() ? 'Découvre toute la carte<br>Pepe Chicken' : $titre;
} );

// Bouton d'ajout rapide « + » dans les grilles de produits
add_filter( 'woocommerce_product_add_to_cart_text', function ( $texte, $produit ) {
    if ( is_product() ) {
        return $texte;
    }
    return $produit->is_type( 'simple' ) && $produit->is_purchasable() && $produit->is_in_stock() ? '+' : '→';
}, 10, 2 );

/** Catégories de produits affichées (sans « Non classé »). */
function pepe_categories_produits() {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
    ) );
    return is_wp_error( $categories ) ? array() : $categories;
}

// Onglets de catégories sous le titre de la Boutique
add_action( 'woocommerce_archive_description', function () {
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }
    $categories = pepe_categories_produits();
    if ( empty( $categories ) ) {
        return;
    }
    $courante = is_product_category() ? get_queried_object_id() : 0;

    echo '<nav class="pepe-categories" aria-label="Catégories">';
    printf(
        '<a class="pepe-categories__lien%s" href="%s"%s>Tout</a>',
        $courante ? '' : ' pepe-categories__lien--actif',
        esc_url( wc_get_page_permalink( 'shop' ) ),
        $courante ? '' : ' aria-current="page"'
    );
    foreach ( $categories as $categorie ) {
        $actif = ( $courante === $categorie->term_id );
        printf(
            '<a class="pepe-categories__lien%s" href="%s"%s>%s</a>',
            $actif ? ' pepe-categories__lien--actif' : '',
            esc_url( get_term_link( $categorie ) ),
            $actif ? ' aria-current="page"' : '',
            esc_html( $categorie->name )
        );
    }
    echo '</nav>';
}, 5 );

// Page Boutique : produits regroupés par catégorie (comme la maquette)
function pepe_boutique_par_categorie() {
    foreach ( pepe_categories_produits() as $categorie ) {
        echo '<section class="pepe-section" id="' . esc_attr( $categorie->slug ) . '">';
        echo '<h2 class="pepe-section__titre">' . esc_html( $categorie->name ) . '</h2>';
        echo do_shortcode( '[products category="' . esc_attr( $categorie->slug ) . '" columns="3" limit="12"]' );
        echo '</section>';
    }
}
