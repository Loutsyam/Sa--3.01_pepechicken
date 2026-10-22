<?php
/**
 * IT-6 : page de validation de commande (checkout)
 *
 * US-6.1 : texte « Instructions pour le retrait » réglé dans l'admin
 *          (WooCommerce > Instructions retrait, page d'options SCF)
 * US-6.2 : page de commande comme la maquette, jusqu'au paiement
 *
 * Templates dans woocommerce/checkout/ : form-checkout.php, review-order.php,
 * payment.php, payment-method.php
 */

defined( 'ABSPATH' ) || exit;


/* ---------- US-6.1 : page d'options dans l'admin ---------- */

// Le champ est dans le groupe SCF « Instructions pour le retrait » (acf-json/group_pepe_retrait.json)
function pepe_page_options_retrait() {
    if ( function_exists( 'acf_add_options_page' ) ) {
        acf_add_options_page( array(
            'page_title'  => 'Instructions pour le retrait',
            'menu_title'  => 'Instructions retrait',
            'menu_slug'   => 'pepe-instructions-retrait',
            'parent_slug' => 'woocommerce',
            'capability'  => 'manage_woocommerce',
        ) );
    }
}
add_action( 'acf/init', 'pepe_page_options_retrait' );

// Texte des instructions (vide si rien n'est enregistré)
function pepe_instructions_retrait() {
    if ( ! function_exists( 'get_field' ) ) {
        return '';
    }
    return (string) get_field( 'pepe_instructions_retrait', 'option' );
}


/* ---------- Mode d'expédition choisi dans le panier ---------- */

// Le client a-t-il choisi le retrait en boutique ?
function pepe_est_retrait() {
    $choisis = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods' ) : array();

    if ( empty( $choisis[0] ) ) {
        return false;
    }
    return 0 === strpos( $choisis[0], 'local_pickup' );
}

// Mode d'expédition choisi (objet WooCommerce), ou null
function pepe_expedition_choisie() {
    $paquets = WC()->shipping()->get_packages();
    $choisis = WC()->session ? (array) WC()->session->get( 'chosen_shipping_methods' ) : array();

    if ( empty( $paquets[0]['rates'] ) || empty( $choisis[0] ) || ! isset( $paquets[0]['rates'][ $choisis[0] ] ) ) {
        return null;
    }
    return $paquets[0]['rates'][ $choisis[0] ];
}


/* ---------- Champs « Mes coordonnées » ---------- */

// Retrait : prénom, nom, e-mail, téléphone. Livraison : + adresse, code postal, ville.
function pepe_champs_commande( $champs ) {
    $a_garder = array( 'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone', 'billing_country' );

    if ( ! pepe_est_retrait() ) {
        $a_garder[] = 'billing_address_1';
        $a_garder[] = 'billing_postcode';
        $a_garder[] = 'billing_city';
    }

    foreach ( array_keys( $champs['billing'] ) as $cle ) {
        if ( ! in_array( $cle, $a_garder, true ) ) {
            unset( $champs['billing'][ $cle ] );
        }
    }

    // Le pays est toujours la France : champ caché
    $champs['billing']['billing_country']['type']    = 'hidden';
    $champs['billing']['billing_country']['default'] = 'FR';

    // Comme la maquette : Prénom | Nom puis E-mail | Téléphone
    $champs['billing']['billing_first_name']['placeholder'] = 'Prénom';
    $champs['billing']['billing_last_name']['placeholder']  = 'Nom';
    $champs['billing']['billing_email']['placeholder']      = 'prenom@exemple.fr';
    $champs['billing']['billing_email']['class']            = array( 'form-row-first' );
    $champs['billing']['billing_email']['priority']         = 30;
    $champs['billing']['billing_phone']['placeholder']      = '06 00 00 00 00';
    $champs['billing']['billing_phone']['class']            = array( 'form-row-last' );
    $champs['billing']['billing_phone']['priority']         = 35;
    $champs['billing']['billing_phone']['required']         = true;

    return $champs;
}
add_filter( 'woocommerce_checkout_fields', 'pepe_champs_commande' );

// Une seule adresse (celle de « Mes coordonnées ») : pas de « Livrer à une autre adresse ? »
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );

// Pas de champ « Notes de commande »
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

// Le code promo se met dans le panier : pas de lien « Vous avez un code promo ? » ici
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );


/* ---------- Bloc « Retrait en restaurant » (US-6.1 et US-6.2) ---------- */

function pepe_bloc_retrait() {
    $methode = pepe_expedition_choisie();
    if ( ! $methode ) {
        return;
    }

    $retrait = pepe_est_retrait();
    $prix    = (float) $methode->get_cost() > 0 ? wp_strip_all_tags( wc_price( $methode->get_cost() ) ) : 'gratuit';
    $lieu    = $retrait ? 'Pepe Chicken ' . get_option( 'woocommerce_store_city' ) : 'À l\'adresse indiquée ci-dessus';

    // Même bloc que l'adresse de livraison de WooCommerce (woocommerce-shipping-fields)
    echo '<div class="woocommerce-shipping-fields">';
    echo '<h2>' . ( $retrait ? 'Retrait en restaurant' : 'Livraison à domicile' ) . '</h2>';

    echo '<p>';
    echo '<strong>' . esc_html( $lieu ) . '</strong>';
    echo '<small>' . esc_html( $methode->get_label() . ' · ' . $prix ) . '</small>';
    echo '<a href="' . esc_url( wc_get_cart_url() ) . '">Modifier</a>';
    echo '</p>';

    // US-6.1 : le texte vient de la page d'options « Instructions retrait » (message « info » de WooCommerce)
    $instructions = pepe_instructions_retrait();
    if ( $retrait && $instructions ) {
        echo '<div class="woocommerce-info">';
        echo '<strong>Instructions pour le retrait</strong><br>';
        echo wp_kses_post( $instructions );
        echo '</div>';
    }

    echo '</div>';
}
// Affiché par woocommerce/checkout/form-checkout.php, sous les coordonnées


/* ---------- Paiement et bouton ---------- */

// Le paiement est affiché à gauche (form-checkout.php), pas sous le récapitulatif
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

// À sa place, sous « Ma commande » : le bouton (même bloc place-order que checkout/payment.php)
function pepe_bloc_bouton_commande() {
    ?>
    <div class="form-row place-order">
        <noscript>
            Votre navigateur n'exécute pas JavaScript : cliquez sur « Mettre à jour le total » avant de commander.
            <br><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="Mettre à jour le total">Mettre à jour le total</button>
        </noscript>

        <?php wc_get_template( 'checkout/terms.php' ); ?>

        <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

        <?php $order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ); ?>
        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>
    <?php
}
add_action( 'woocommerce_checkout_order_review', 'pepe_bloc_bouton_commande', 20 );

// Bouton jaune « Passer la commande 32,85 € »
// (le texte est aussi dans data-value : le script de WooCommerce le remet à chaque mise à jour)
function pepe_bouton_commande( $html ) {
    $texte = 'Passer la commande ' . html_entity_decode( wp_strip_all_tags( WC()->cart->get_total() ) );

    return '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $texte ) . '" data-value="' . esc_attr( $texte ) . '">' . esc_html( $texte ) . '</button>';
}
add_filter( 'woocommerce_order_button_html', 'pepe_bouton_commande' );


/* ---------- Titre de la page et étapes ---------- */

// Titre de Storefront (entry-title) : « Finaliser ma commande »
function pepe_titre_commande( $titre, $id = 0 ) {
    if ( is_checkout() && ! is_wc_endpoint_url() && in_the_loop() && wc_get_page_id( 'checkout' ) === $id ) {
        return 'Finaliser ma commande';
    }
    return $titre;
}
add_filter( 'the_title', 'pepe_titre_commande', 10, 2 );

// Étapes de la commande sous le titre (fonction dans inc/panier.php), étape 2
function pepe_etapes_validation() {
    pepe_etapes_commande( 2 );
}
add_action( 'woocommerce_before_checkout_form', 'pepe_etapes_validation', 5 );
