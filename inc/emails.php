<?php
/**
 * IT-8 : e-mails transactionnels
 *
 * US-8.1 : réglages dans WooCommerce > Réglages > E-mails (expéditeur, couleurs, pied)
 * US-8.2 : templates surchargés dans woocommerce/emails/ (email-header.php, email-footer.php)
 *          + couleurs de l'enseigne ci-dessous
 * US-8.3 : instructions de retrait dans les e-mails des commandes à retirer en boutique
 */

defined( 'ABSPATH' ) || exit;


/* ---------- US-8.2 : couleurs et polices de l'enseigne ---------- */

// CSS ajouté après celui de WooCommerce (il est ensuite mis « en ligne » dans l'e-mail)
function pepe_styles_emails( $css ) {
    $rouge = '#d81f2a';
    $jaune = '#ffc700';
    $beige = '#ffecd9';
    $brun  = '#3d2120';
    $titre = '"Cooper Black", "Cooper Std", Georgia, serif';
    $texte = '"Myriad Pro", "Helvetica Neue", Arial, sans-serif';

    $css .= "
    body, #outer_wrapper { background-color: $beige; }
    #wrapper { padding: 24px 0; }
    #template_container { border: 0; border-radius: 20px; overflow: hidden; background-color: #ffffff; }
    #body_content { background-color: #ffffff; }
    #body_content_inner, #body_content_inner p, #body_content_inner td, #body_content_inner th { color: $brun; font-family: $texte; font-size: 15px; }

    #template_header_image { padding: 8px 0 20px; text-align: center; }
    #template_header_image .email-logo-text { margin: 0; font-family: $titre; font-size: 44px; line-height: 1; text-align: center; }
    #template_header_image .email-logo-text a { color: $rouge; font-weight: bold; line-height: 1; text-decoration: none; }
    #template_header_image .email-logo-text small { display: block; font-size: 18px; font-weight: normal; line-height: 1; }

    #template_header { background-color: $rouge; border-radius: 0; }
    #header_wrapper { padding: 28px 40px; }
    #header_wrapper h1 { margin: 0; font-family: $titre; font-size: 28px; font-weight: normal; line-height: 1.2; color: $beige; text-align: left; text-shadow: none; }

    h2, h3 { font-family: $titre; font-weight: normal; color: $rouge; }
    a, .link { color: $rouge; }
    .td { border-color: #f5dcc4; color: $brun; }
    .order_item td { border-color: #f5dcc4; }
    tfoot .order-totals-total th, tfoot .order-totals-total td { color: $rouge; }

    .email-additional-content.pepe-retrait { margin: 0 0 24px; padding: 16px 20px; border-left: 4px solid $jaune; border-radius: 12px; background-color: $beige; }
    .email-additional-content.pepe-retrait h2 { margin: 0 0 8px; font-size: 20px; }
    .email-additional-content.pepe-retrait p { margin: 0 0 6px; text-align: left; }

    #template_footer { background-color: $brun; border-radius: 20px; }
    #template_footer #credit, #template_footer #credit p { color: $beige; font-family: $texte; font-size: 13px; text-align: center; }
    #template_footer #credit a { color: $jaune; font-weight: bold; text-decoration: none; }
    ";

    return $css;
}
add_filter( 'woocommerce_email_styles', 'pepe_styles_emails', 20 );


/* ---------- US-8.3 : instructions de retrait ---------- */

// La commande est-elle à retirer en boutique ?
function pepe_commande_a_retirer( $commande ) {
    return $commande instanceof WC_Order && $commande->has_shipping_method( 'local_pickup' );
}

// E-mails envoyés au client où le retrait est utile
function pepe_emails_avec_retrait() {
    return array( 'customer_on_hold_order', 'customer_processing_order', 'customer_ready_order', 'customer_invoice' );
}

// Bloc « Instructions pour le retrait » au-dessus du détail de la commande
function pepe_retrait_dans_email( $commande, $pour_admin, $texte_brut, $email ) {
    if ( $pour_admin || ! $email || ! in_array( $email->id, pepe_emails_avec_retrait(), true ) ) {
        return;
    }

    if ( ! pepe_commande_a_retirer( $commande ) ) {
        return;
    }

    $instructions = pepe_instructions_retrait();
    if ( '' === $instructions ) {
        return;
    }

    if ( $texte_brut ) {
        echo "\n" . strtoupper( 'Instructions pour le retrait' ) . "\n\n";
        echo wp_strip_all_tags( $instructions ) . "\n\n";
        echo "----------------------------------------\n\n";
        return;
    }

    // email-additional-content : classe de WooCommerce pour un bloc de texte en plus dans l'e-mail
    echo '<div class="email-additional-content pepe-retrait">';
    echo '<h2>Instructions pour le retrait</h2>';
    echo wp_kses_post( wpautop( $instructions ) );
    echo '</div>';
}
add_action( 'woocommerce_email_order_details', 'pepe_retrait_dans_email', 5, 4 );
