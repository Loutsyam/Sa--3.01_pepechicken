<?php
/**
 * E-mail « Ta commande est prête » (IT-9 – US-9.2)
 * Les instructions de retrait sont ajoutées par inc/emails.php (US-8.3).
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>Bonjour <?php echo esc_html( $order->get_billing_first_name() ); ?>,</p>
<p>Bonne nouvelle : ta commande <strong><?php echo esc_html( $order->get_order_number() ); ?></strong> est prête. Tu peux venir la récupérer au comptoir « Click &amp; Collect ».</p>
<?php if ( 'cod' === $order->get_payment_method() ) : ?>
	<p>Tu régleras <strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong> sur place, au moment du retrait.</p>
<?php endif; ?>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
