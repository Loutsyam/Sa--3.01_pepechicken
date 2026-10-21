<?php
/**
 * Bloc « Avis clients » de la fiche produit (IT-14 – US-14.1, US-14.2)
 * Surcharge de woocommerce/templates/single-product-reviews.php :
 * à gauche la note moyenne et le bouton, à droite les avis puis le formulaire.
 * Fonctions dans inc/avis.php.
 *
 * @version 11.2.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! comments_open() ) {
	return;
}

$nombre   = $product->get_review_count();
$moyenne  = $product->get_average_rating();
$peut     = pepe_peut_laisser_avis( $product );
$verifies = ( 'yes' === get_option( 'woocommerce_review_rating_verification_required' ) );
?>
<section id="reviews" class="woocommerce-Reviews pepe-avis" aria-labelledby="pepe-avis-titre">

	<div class="pepe-avis__resume">
		<h2 class="pepe-avis__titre" id="pepe-avis-titre">Avis clients</h2>

		<?php if ( $nombre && wc_review_ratings_enabled() ) : ?>
			<p class="pepe-avis__moyenne"><?php echo esc_html( number_format_i18n( $moyenne, 1 ) ); ?></p>
			<?php echo wc_get_rating_html( $moyenne, $nombre ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p class="pepe-avis__nombre">
				<?php
				$texte = $nombre . ' avis';
				if ( $verifies ) {
					$texte .= ( $nombre > 1 ) ? ' vérifiés' : ' vérifié';
				}
				echo esc_html( $texte );
				?>
			</p>
		<?php else : ?>
			<p class="pepe-avis__nombre">Pas encore d'avis</p>
		<?php endif; ?>

		<?php if ( $peut ) : ?>
			<a class="pepe-avis__bouton" href="#review_form_wrapper">Laisser un avis</a>
		<?php endif; ?>
	</div>

	<div id="comments" class="pepe-avis__liste">
		<?php if ( have_comments() ) : ?>
			<ol class="commentlist pepe-avis__cartes">
				<?php wp_list_comments( array( 'callback' => 'pepe_carte_avis' ) ); ?>
			</ol>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links( array( 'type' => 'list' ) );
				echo '</nav>';
			}
			?>
		<?php else : ?>
			<p class="woocommerce-noreviews pepe-avis__vide">Personne n'a encore donné son avis sur ce produit.</p>
		<?php endif; ?>

		<?php if ( $peut ) : ?>
			<div id="review_form_wrapper" class="pepe-avis__formulaire">
				<div id="review_form">
					<?php
					$commenter = wp_get_current_commenter();
					$requis    = (bool) get_option( 'require_name_email', 1 );

					$formulaire = array(
						'title_reply'         => have_comments() ? 'Laisser un avis' : 'Sois le premier à donner ton avis',
						'title_reply_to'      => 'Répondre à %s',
						'title_reply_before'  => '<h3 id="reply-title" class="comment-reply-title pepe-avis__sous-titre">',
						'title_reply_after'   => '</h3>',
						'comment_notes_after' => '',
						'label_submit'        => 'Publier mon avis',
						'class_submit'        => 'submit button',
						'logged_in_as'        => '',
						'comment_field'       => '',
						'must_log_in'         => '<p class="must-log-in">Connecte-toi à <a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">ton compte</a> pour laisser un avis.</p>',
						'fields'              => array(
							'author' => '<p class="comment-form-author"><label for="author">Prénom et nom' . ( $requis ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" autocomplete="name" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . ( $requis ? ' required' : '' ) . '></p>',
							'email'  => '<p class="comment-form-email"><label for="email">E-mail' . ( $requis ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" autocomplete="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . ( $requis ? ' required' : '' ) . '></p>',
						),
					);

					// Note de 1 à 5 (WooCommerce transforme la liste en étoiles cliquables)
					if ( wc_review_ratings_enabled() ) {
						$formulaire['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">Ta note' . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
							<option value="">Choisir…</option>
							<option value="5">Parfait</option>
							<option value="4">Bon</option>
							<option value="3">Moyen</option>
							<option value="2">Pas terrible</option>
							<option value="1">Décevant</option>
						</select></div>';
					}

					$formulaire['comment_field'] .= '<p class="comment-form-comment"><label for="comment">Ton commentaire&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="5" required></textarea></p>';

					comment_form( $formulaire );
					?>
				</div>
			</div>
		<?php elseif ( ! is_user_logged_in() ) : ?>
			<p class="woocommerce-verification-required pepe-avis__info">Tu as goûté ce produit ? <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Connecte-toi</a> pour laisser un avis.</p>
		<?php else : ?>
			<p class="woocommerce-verification-required pepe-avis__info">Seuls les clients qui ont commandé ce produit peuvent laisser un avis.</p>
		<?php endif; ?>
	</div>

</section>
