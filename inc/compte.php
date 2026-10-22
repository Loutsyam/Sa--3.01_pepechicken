<?php
/**
 * IT-7 : espace client (Mon compte)
 *
 * Template dans woocommerce/myaccount/ : dashboard.php (mêmes classes que WooCommerce).
 * Le reste (menu, connexion, commandes, adresses…) garde le HTML de WooCommerce, stylé en CSS.
 */

defined( 'ABSPATH' ) || exit;

// Menu du compte : les noms de la maquette, sans « Téléchargements » ni « Moyens de paiement »
function pepe_menu_compte( $liens ) {
    return array(
        'dashboard'       => 'Tableau de bord',
        'orders'          => 'Mes commandes',
        'edit-address'    => 'Adresses',
        'edit-account'    => 'Détails du compte',
        'customer-logout' => 'Déconnexion',
    );
}
add_filter( 'woocommerce_account_menu_items', 'pepe_menu_compte' );

// Numéro de commande comme la maquette : « PC-1042 »
function pepe_numero_commande( $numero ) {
    return 'PC-' . $numero;
}
add_filter( 'woocommerce_order_number', 'pepe_numero_commande' );

// Connecté : le titre « Mon compte » devient « Bonjour Prénom 👋 » comme la maquette
// (on garde le titre de la page de Storefront, h1.entry-title)
function pepe_titre_compte( $titre, $id = 0 ) {
    if ( is_account_page() && is_user_logged_in() && in_the_loop() && wc_get_page_id( 'myaccount' ) === $id ) {
        // Prénom du compte, sinon celui de la dernière commande, sinon le nom affiché
        $client = wp_get_current_user();
        $prenom = $client->first_name;
        if ( ! $prenom ) {
            $prenom = get_user_meta( $client->ID, 'billing_first_name', true );
        }
        if ( ! $prenom ) {
            $prenom = $client->display_name;
        }
        return 'Bonjour ' . esc_html( $prenom ) . ' 👋';
    }
    return $titre;
}
add_filter( 'the_title', 'pepe_titre_compte', 10, 2 );

// Statut affiché au client
function pepe_statut_commande( $commande ) {
    $statuts = array(
        'pending'    => 'En attente de paiement',
        'on-hold'    => 'Reçue',
        'processing' => 'En préparation',
        'prete'      => 'Prête à retirer',
        'completed'  => 'Récupérée',
        'cancelled'  => 'Annulée',
        'refunded'   => 'Remboursée',
        'failed'     => 'Échouée',
    );

    $statut = $commande->get_status();
    if ( isset( $statuts[ $statut ] ) ) {
        return $statuts[ $statut ];
    }
    return wc_get_order_status_name( $statut );
}

// Étape de la barre de progression (sur 4) : reçue, en préparation, prête, récupérée
function pepe_etape_commande( $commande ) {
    switch ( $commande->get_status() ) {
        case 'processing':
            return 2;
        case 'prete':
            return 3;
        case 'completed':
            return 4;
        default:
            return 1;
    }
}
