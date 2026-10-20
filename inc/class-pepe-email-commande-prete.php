<?php
/**
 * IT-9 – US-9.2 : e-mail « Ta commande est prête » envoyé au client
 * quand la commande passe au statut « Prête à retirer ».
 *
 * Réglable comme les autres dans WooCommerce > Réglages > E-mails.
 * Templates : woocommerce/emails/customer-ready-order.php (+ version texte dans plain/)
 */

defined( 'ABSPATH' ) || exit;

class Pepe_Email_Commande_Prete extends WC_Email {

    public function __construct() {
        $this->id             = 'customer_ready_order';
        $this->customer_email = true;
        $this->title          = 'Commande prête à retirer';
        $this->description    = 'Envoyé au client quand sa commande passe au statut « Prête à retirer ».';
        $this->template_html  = 'emails/customer-ready-order.php';
        $this->template_plain = 'emails/plain/customer-ready-order.php';
        $this->template_base  = get_stylesheet_directory() . '/woocommerce/';
        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
        );

        add_action( 'woocommerce_order_status_prete_notification', array( $this, 'trigger' ), 10, 2 );

        parent::__construct();
    }

    public function get_default_subject() {
        return 'Ta commande {order_number} est prête !';
    }

    public function get_default_heading() {
        return 'Ta commande est prête';
    }

    public function get_default_additional_content() {
        return 'À tout de suite chez Pepe Chicken !';
    }

    public function trigger( $commande_id, $commande = false ) {
        $this->setup_locale();

        if ( $commande_id && ! is_a( $commande, 'WC_Order' ) ) {
            $commande = wc_get_order( $commande_id );
        }

        if ( is_a( $commande, 'WC_Order' ) ) {
            $this->object                         = $commande;
            $this->recipient                      = $commande->get_billing_email();
            $this->placeholders['{order_date}']   = wc_format_datetime( $commande->get_date_created() );
            $this->placeholders['{order_number}'] = $commande->get_order_number();
        }

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();
    }

    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => false,
                'plain_text'         => false,
                'email'              => $this,
            ),
            '',
            $this->template_base
        );
    }

    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => false,
                'plain_text'         => true,
                'email'              => $this,
            ),
            '',
            $this->template_base
        );
    }
}
