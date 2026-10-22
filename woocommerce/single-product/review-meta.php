<?php
/**
 * Auteur, achat vérifié et date d'un avis (IT-14 – US-14.1)
 * Surcharge de woocommerce/templates/single-product/review-meta.php : mêmes classes,
 * texte de la maquette (« Achat vérifié · il y a 2 jours »). Le nom « Prénom N. » vient de inc/avis.php.
 *
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $comment;
$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta">
		<em class="woocommerce-review__awaiting-approval">Merci ! Ton avis sera visible dès qu’il aura été validé.</em>
	</p>

<?php } else { ?>

	<p class="meta">
		<strong class="woocommerce-review__author"><?php comment_author(); ?></strong>
		<?php if ( 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) && $verified ) : ?>
			<em class="woocommerce-review__verified verified">Achat vérifié</em>
			<span class="woocommerce-review__dash">·</span>
		<?php endif; ?>
		<time class="woocommerce-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>">il y a <?php echo esc_html( human_time_diff( get_comment_date( 'U' ), time() ) ); ?></time>
	</p>

<?php
}
