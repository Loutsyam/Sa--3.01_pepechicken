<?php
/**
 * E-mail « Ta commande est prête », version texte (IT-9 – US-9.2)
 */

defined( 'ABSPATH' ) || exit;

echo '= ' . esc_html( wp_strip_all_tags( $email_heading ) ) . " =\n\n";

echo 'Bonjour ' . esc_html( $order->get_billing_first_name() ) . ",\n\n";
echo 'Bonne nouvelle : ta commande ' . esc_html( $order->get_order_number() ) . " est prête. Tu peux venir la récupérer au comptoir « Click & Collect ».\n\n";

if ( 'cod' === $order->get_payment_method() ) {
	echo 'Tu régleras ' . esc_html( wp_strip_all_tags( $order->get_formatted_order_total() ) ) . " sur place, au moment du retrait.\n\n";
}

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) ) . "\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
