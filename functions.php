<?php
/**
 * Thème enfant Pepe Chicken (thème parent : Storefront)
 *
 * On garde Storefront (menu mobile, mini-panier…) et on ajoute nos éléments.
 * On reprend le HTML et les classes de Storefront et de WooCommerce ; les classes « pepe- »
 * (convention BEM) ne servent que pour ce qui n'existe pas dans Storefront.
 * Le fichier style.css est chargé automatiquement par Storefront.
 */

defined( 'ABSPATH' ) || exit;

// Fichiers du thème
require_once get_stylesheet_directory() . '/inc/accueil.php';                // IT-2 : page d'accueil
require_once get_stylesheet_directory() . '/inc/options-affichage.php';      // IT-3 : options sur la fiche produit
require_once get_stylesheet_directory() . '/inc/options-prix.php';           // IT-3 : prix, panier et commande
require_once get_stylesheet_directory() . '/inc/valeurs-nutritionnelles.php'; // IT-4 : valeurs nutritionnelles
require_once get_stylesheet_directory() . '/inc/panier.php';                 // IT-5 : page Panier
require_once get_stylesheet_directory() . '/inc/commande.php';               // IT-6 : validation de commande
require_once get_stylesheet_directory() . '/inc/compte.php';                 // IT-7 : espace client
require_once get_stylesheet_directory() . '/inc/emails.php';                 // IT-8 : e-mails transactionnels
require_once get_stylesheet_directory() . '/inc/suivi-commandes.php';        // IT-9 : suivi des commandes
require_once get_stylesheet_directory() . '/inc/promotions.php';             // IT-10 : promotions
require_once get_stylesheet_directory() . '/inc/equipe.php';                 // IT-11 : équipe
require_once get_stylesheet_directory() . '/inc/collections.php';            // IT-12 : collections
require_once get_stylesheet_directory() . '/inc/ventes-croisees.php';        // IT-13 : ventes croisées
require_once get_stylesheet_directory() . '/inc/avis.php';                   // IT-14 : avis clients
require_once get_stylesheet_directory() . '/inc/frais.php';                  // IT-15 : frais de préparation


/* ---------- Header et footer de Storefront ---------- */

function pepe_reglages_storefront() {
    // On enlève la recherche, le menu secondaire et la barre latérale (pas sur la maquette)
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
    remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );

    // Le panier de Storefront est remplacé par nos boutons (Commander, compte, panier)
    remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    add_action( 'storefront_header', 'pepe_header_actions', 60 );

    // Les crédits du footer sont remplacés par notre footer
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'pepe_footer', 20 );
}
add_action( 'init', 'pepe_reglages_storefront' );


/* ---------- Header ---------- */

// Logo : même HTML que Storefront (site-title + site-description), sur 2 lignes « PEPE / Chicken® »
function storefront_site_branding() {
    echo '<div class="site-branding">';

    if ( has_custom_logo() ) {
        the_custom_logo();
    } else {
        echo '<div class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">PEPE</a></div>';
        echo '<p class="site-description">Chicken®</p>';
    }

    echo '</div>';
}

// À droite du header : bouton Commander et bouton compte (menu secondaire de Storefront), puis le panier
function pepe_header_actions() {
    $compte = wc_get_page_permalink( 'myaccount' ); // connexion si le visiteur n'est pas connecté
    $texte  = is_user_logged_in() ? 'Mon compte' : 'Se connecter';
    ?>
    <nav class="secondary-navigation" aria-label="Commander et compte">
        <ul class="menu">
            <li class="menu-item"><a class="button alt" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Commander</a></li>
            <li class="menu-item my-account">
                <a href="<?php echo esc_url( $compte ); ?>" aria-label="<?php echo esc_attr( $texte ); ?>">
                    <svg viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="10" cy="5" r="4"/><path d="M2 19v-2a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v2z"/></svg>
                </a>
            </li>
        </ul>
        <?php storefront_header_cart(); ?>
    </nav>
    <?php
}

// Menu affiché tant qu'aucun menu n'est choisi dans Apparence > Menus
function pepe_menu_si_vide( $args ) {
    if ( in_array( $args['theme_location'], array( 'primary', 'handheld' ), true ) && ! has_nav_menu( $args['theme_location'] ) ) {
        $args['fallback_cb'] = 'pepe_menu_par_defaut';
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'pepe_menu_si_vide' );

function pepe_menu_par_defaut( $args ) {
    $classe = $args['container_class'] ? $args['container_class'] : 'menu';

    echo '<div class="' . esc_attr( $classe ) . '"><ul class="menu">';
    echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">Le concept</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Le menu</a></li>';
    echo '<li class="menu-item"><a href="' . esc_url( pepe_url_a_propos() ) . '">À propos</a></li>';
    echo '</ul></div>';
}


/* ---------- Footer ---------- */

// Lien d'une page à partir de son slug (« # » si la page n'existe pas encore)
function pepe_url_page( $slug ) {
    $page = get_page_by_path( $slug );

    if ( $page ) {
        return get_permalink( $page );
    }
    return '#';
}

// IT-11 : « À propos » = page de l'équipe (archive du type de contenu « membre »)
function pepe_url_a_propos() {
    if ( post_type_exists( 'membre' ) ) {
        return get_post_type_archive_link( 'membre' );
    }
    return pepe_url_page( 'a-propos' );
}

function pepe_footer() {
    $cgv = wc_terms_and_conditions_page_id() ? get_permalink( wc_terms_and_conditions_page_id() ) : pepe_url_page( 'cgv' );
    $confidentialite = get_privacy_policy_url() ? get_privacy_policy_url() : '#';

    // Titre de la colonne => liens (texte => adresse)
    $colonnes = array(
        'Accès rapide' => array(
            'Le concept' => home_url( '/' ),
            'Le menu'    => wc_get_page_permalink( 'shop' ),
            'À propos'   => pepe_url_a_propos(),
            'Mon compte' => wc_get_page_permalink( 'myaccount' ),
            'FAQ'        => pepe_url_page( 'faq' ),
        ),
        'Services consommateurs' => array(
            'Informations allergènes' => pepe_url_page( 'allergenes' ),
            'Origine des viandes'     => pepe_url_page( 'origine-des-viandes' ),
        ),
        'Contact' => array(
            'Nous contacter' => pepe_url_page( 'contact' ),
        ),
        'Informations légales' => array(
            'Mentions légales'             => pepe_url_page( 'mentions-legales' ),
            'CGV'                          => $cgv,
            'Politique de confidentialité' => $confidentialite,
        ),
    );

    // Même HTML que les widgets du footer de Storefront (footer-widgets, block, widget, widget-title)
    echo '<div class="footer-widgets row-1 col-5 fix">';

    $numero = 1;
    foreach ( $colonnes as $titre => $liens ) {
        // Le logo est au milieu, après les 2 premières colonnes (comme la maquette)
        if ( 3 === $numero ) {
            echo '<div class="block footer-widget-3 site-branding">';
            echo '<div class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">PEPE</a></div>';
            echo '<p class="site-description">Chicken®</p>';
            echo '</div>';
            $numero++;
        }

        echo '<div class="block footer-widget-' . esc_attr( $numero ) . '">';
        echo '<div class="widget widget_nav_menu">';
        echo '<span class="gamma widget-title">' . esc_html( $titre ) . '</span>';
        echo '<ul class="menu">';
        foreach ( $liens as $texte => $url ) {
            echo '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $texte ) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>';
        echo '</div>';

        $numero++;
    }

    echo '</div>';
    echo '<div class="site-info">Pour votre santé, pratiquez une activité physique régulière : <a href="https://www.mangerbouger.fr" target="_blank" rel="noopener">www.mangerbouger.fr</a></div>';
}


/* ---------- Boutique WooCommerce ---------- */

// 3 produits par ligne
function pepe_colonnes_boutique() {
    return 3;
}
add_filter( 'loop_shop_columns', 'pepe_colonnes_boutique' );

// Titre de la page Boutique
function pepe_titre_boutique( $titre ) {
    if ( is_shop() ) {
        return 'Découvre toute la carte<br>Pepe Chicken';
    }
    return $titre;
}
add_filter( 'woocommerce_page_title', 'pepe_titre_boutique' );

// Bouton « + » dans les grilles de produits (« → » si le produit s'ouvre d'abord)
function pepe_texte_bouton_ajout( $texte, $produit ) {
    // Sur la fiche produit, le vrai bouton garde son texte (sauf dans « Ça va bien avec », IT-13)
    if ( is_product() && 'related' !== wc_get_loop_prop( 'name' ) ) {
        return $texte;
    }

    if ( $produit->is_type( 'simple' ) && $produit->is_purchasable() && $produit->is_in_stock() ) {
        return '+';
    }
    return '→';
}
add_filter( 'woocommerce_product_add_to_cart_text', 'pepe_texte_bouton_ajout', 10, 2 );

// Catégories de produits (sans « Non classé »)
function pepe_categories_produits() {
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
    ) );

    if ( is_wp_error( $categories ) ) {
        return array();
    }
    return $categories;
}

// Onglets des catégories sous le titre de la Boutique
// (même HTML que le widget « Catégories de produits » de WooCommerce : product-categories, cat-item, current-cat)
function pepe_onglets_categories() {
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }

    $categories = pepe_categories_produits();
    if ( empty( $categories ) ) {
        return;
    }

    $courante = is_product_category() ? get_queried_object_id() : 0;

    echo '<nav class="widget woocommerce widget_product_categories" aria-label="Catégories">';
    echo '<ul class="product-categories">';

    // Onglet « Tout »
    $classe = $courante ? 'cat-item' : 'cat-item current-cat';
    echo '<li class="' . esc_attr( $classe ) . '"><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '"' . ( $courante ? '' : ' aria-current="page"' ) . '>Tout</a></li>';

    // Un onglet par catégorie
    foreach ( $categories as $categorie ) {
        $actif  = ( $courante === $categorie->term_id );
        $classe = 'cat-item cat-item-' . $categorie->term_id . ( $actif ? ' current-cat' : '' );
        echo '<li class="' . esc_attr( $classe ) . '"><a href="' . esc_url( get_term_link( $categorie ) ) . '"' . ( $actif ? ' aria-current="page"' : '' ) . '>' . esc_html( $categorie->name ) . '</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
}
add_action( 'woocommerce_archive_description', 'pepe_onglets_categories', 5 );

// Page Boutique : les produits regroupés par catégorie (utilisé par woocommerce/archive-product.php)
function pepe_boutique_par_categorie() {
    foreach ( pepe_categories_produits() as $categorie ) {
        // Même HTML que les sections de la page d'accueil de Storefront
        echo '<section class="storefront-product-section" id="' . esc_attr( $categorie->slug ) . '">';
        echo '<h2 class="section-title">' . esc_html( $categorie->name ) . '</h2>';
        echo do_shortcode( '[products category="' . esc_attr( $categorie->slug ) . '" columns="3" limit="12"]' );
        echo '</section>';
    }
}
