<?php
/**
 * IT-9 : suivi des commandes
 *
 * US-9.1 : statut « Prête à retirer » dans l'admin (entre « En cours » et « Terminée »)
 * US-9.2 : e-mail au client quand sa commande est prête (avec les instructions de retrait)
 * US-9.3 : le client peut annuler sa commande depuis son compte ; si elle était payée,
 *          le remboursement est enregistré et le client reçoit l'e-mail de remboursement
 */

defined( 'ABSPATH' ) || exit;


/* ---------- US-9.1 : statut « Prête à retirer » ---------- */

function pepe_statut_prete( $statuts ) {
    $statuts['wc-prete'] = array(
        'label'                     => 'Prête à retirer',
        'public'                    => false,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        /* translators: %s: nombre de commandes */
        'label_count'               => _n_noop( 'Prête à retirer <span class="count">(%s)</span>', 'Prêtes à retirer <span class="count">(%s)</span>' ),
    );
    return $statuts;
}
add_filter( 'woocommerce_register_shop_order_post_statuses', 'pepe_statut_prete' );

// Ajouté dans la liste des statuts, juste après « En cours »
function pepe_liste_statuts( $statuts ) {
    $nouveaux = array();
    foreach ( $statuts as $cle => $nom ) {
        $nouveaux[ $cle ] = $nom;
        if ( 'wc-processing' === $cle ) {
            $nouveaux['wc-prete'] = 'Prête à retirer';
        }
    }
    return $nouveaux;
}
add_filter( 'wc_order_statuses', 'pepe_liste_statuts' );

// Action groupée « Changer le statut en Prête à retirer » dans la liste des commandes
function pepe_action_groupee_prete( $actions ) {
    $actions['mark_prete'] = 'Changer le statut en Prête à retirer';
    return $actions;
}
add_filter( 'bulk_actions-woocommerce_page_wc-orders', 'pepe_action_groupee_prete' );
add_filter( 'bulk_actions-edit-shop_order', 'pepe_action_groupee_prete' );

/* ---------- US-9.2 : e-mail « Ta commande est prête » ---------- */

function pepe_email_commande_prete( $emails ) {
    require_once get_stylesheet_directory() . '/inc/class-pepe-email-commande-prete.php';
    $emails['Pepe_Email_Commande_Prete'] = new Pepe_Email_Commande_Prete();
    return $emails;
}
add_filter( 'woocommerce_email_classes', 'pepe_email_commande_prete' );

// L'e-mail part quand le statut passe à « Prête à retirer »
function pepe_actions_emails( $actions ) {
    $actions[] = 'woocommerce_order_status_prete';
    return $actions;
}
add_filter( 'woocommerce_email_actions', 'pepe_actions_emails' );


/* ---------- US-9.3 : annulation par le client ---------- */

// Le client peut annuler tant que la commande n'est pas prête
function pepe_statuts_annulables( $statuts ) {
    $statuts[] = 'on-hold';
    $statuts[] = 'processing';
    return $statuts;
}
add_filter( 'woocommerce_valid_order_statuses_for_cancel', 'pepe_statuts_annulables' );

// Commande annulée par le client : si elle était payée, on enregistre le remboursement
// (WooCommerce envoie alors l'e-mail « Commande remboursée » ; le virement retour est fait à la main)
function pepe_rembourser_annulation( $commande_id ) {
    $commande = wc_get_order( $commande_id );

    if ( ! $commande || ! $commande->get_date_paid() || $commande->get_remaining_refund_amount() <= 0 ) {
        return;
    }

    // Paiement au retrait : rien n'a encore été payé, donc rien à rembourser
    if ( 'cod' === $commande->get_payment_method() ) {
        return;
    }

    wc_create_refund( array(
        'order_id'       => $commande_id,
        'amount'         => $commande->get_remaining_refund_amount(),
        'reason'         => 'Commande annulée par le client',
        'refund_payment' => false,
        'restock_items'  => false, // le stock est déjà remis par l'annulation
    ) );
}
add_action( 'woocommerce_cancelled_order', 'pepe_rembourser_annulation' );

// Message affiché au client après l'annulation
function pepe_message_annulation( $message ) {
    return 'Ta commande a bien été annulée. Tu vas recevoir un e-mail de confirmation.';
}
add_filter( 'woocommerce_order_cancelled_notice', 'pepe_message_annulation' );
