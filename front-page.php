<?php
/**
 * Page d'accueil « Le concept » (IT-2 – US-2.2), conforme à la maquette :
 * en-tête, nos classiques, nos nouveautés, produit à la une, le concept, « C'est validé ! »
 * Textes et images : groupe de champs SCF « Page d'accueil ». Fonctions : inc/accueil.php
 *
 * HTML repris de Storefront et WooCommerce : entry-header / entry-title (titre d'une page),
 * sections storefront-product-section / section-title (accueil de Storefront),
 * shortcodes [product_categories] et [products], fiche produit (product, summary, price),
 * avis (commentlist, review, comment-text, star-rating). Classes pepe- seulement pour
 * la bande « C'est validé ! » et le sondage, qui n'existent pas dans Storefront.
 */

get_header();

$image         = pepe_accueil( 'accueil_image' );
$lien_bouton   = pepe_accueil( 'accueil_bouton_lien' );
$lien_bouton   = $lien_bouton ? $lien_bouton : wc_get_page_permalink( 'shop' );
$vedette       = wc_get_product( pepe_accueil( 'accueil_vedette' ) );
$photo_concept = pepe_accueil( 'accueil_concept_image' );
$photos        = pepe_accueil( 'accueil_photos' );
$avis          = pepe_avis_accueil( 4 );
$sondage       = pepe_sondage_burgers();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php // 1. En-tête : comme le titre d'une page de Storefront ?>
		<header class="entry-header">
			<div>
				<h1 class="entry-title"><?php echo esc_html( pepe_accueil( 'accueil_titre' ) ); ?></h1>
				<p><?php echo esc_html( pepe_accueil( 'accueil_texte' ) ); ?></p>
				<?php if ( pepe_accueil( 'accueil_petite_ligne' ) ) : ?>
					<p><small><?php echo esc_html( pepe_accueil( 'accueil_petite_ligne' ) ); ?></small></p>
				<?php endif; ?>
				<a class="button" href="<?php echo esc_url( $lien_bouton ); ?>"><?php echo esc_html( pepe_accueil( 'accueil_bouton_texte' ) ); ?> ›</a>
			</div>
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
			<?php endif; ?>
		</header>

		<?php // 2. Découvre nos classiques : les catégories avec le shortcode de WooCommerce ?>
		<section class="storefront-product-section storefront-product-categories" aria-labelledby="titre-classiques">
			<h2 class="section-title" id="titre-classiques">Découvre nos classiques</h2>
			<?php
			$ids_categories = wp_list_pluck( pepe_categories_produits(), 'term_id' );
			echo do_shortcode( '[product_categories ids="' . esc_attr( implode( ',', $ids_categories ) ) . '" orderby="include" columns="4"]' );
			?>
			<button type="button" aria-label="Catégories suivantes">›</button>
			<p>
				<a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Voir toute la carte et commander</a>
			</p>
		</section>

		<?php // 3. Nos nouveautés : mêmes cartes que la Boutique ?>
		<section class="storefront-product-section storefront-recent-products" aria-labelledby="titre-nouveautes">
			<h2 class="section-title" id="titre-nouveautes">Nos nouveautés</h2>
			<?php
			$ids = pepe_nouveautes();
			if ( $ids ) {
				echo do_shortcode( '[products ids="' . esc_attr( implode( ',', $ids ) ) . '" columns="3" orderby="post__in"]' );
			}
			?>
		</section>

		<?php // 4. Produit à la une : même HTML qu'une fiche produit (images, summary, price) ?>
		<?php if ( $vedette ) : ?>
			<section class="storefront-product-section storefront-featured-products" aria-labelledby="titre-vedette">
				<div class="product">
					<div class="images">
						<?php echo $vedette->get_image( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="summary entry-summary">
						<?php if ( pepe_accueil( 'accueil_vedette_badge' ) ) : ?>
							<span class="onsale"><?php echo esc_html( pepe_accueil( 'accueil_vedette_badge' ) ); ?></span>
						<?php endif; ?>
						<h2 class="product_title entry-title" id="titre-vedette"><?php echo esc_html( $vedette->get_name() ); ?></h2>
						<?php $description = wp_strip_all_tags( $vedette->get_short_description() ? $vedette->get_short_description() : $vedette->get_description() ); ?>
						<?php if ( $description ) : ?>
							<div class="woocommerce-product-details__short-description">
								<p><?php echo esc_html( $description ); ?></p>
							</div>
						<?php endif; ?>
						<p class="price">
							<?php echo wp_kses_post( $vedette->get_price_html() ); ?>
							<a class="button alt" href="<?php echo esc_url( $vedette->get_permalink() ); ?>">Commander</a>
						</p>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php // 5. Le concept : comme un article de Storefront (hentry, post-thumbnail, entry-content) ?>
		<article class="hentry" id="concept" aria-labelledby="titre-concept">
			<?php // La vague en haut, et la forme de flamme utilisée pour la photo (clip-path dans style.css) ?>
			<svg viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" focusable="false">
				<defs>
					<clipPath id="pepe-flamme" clipPathUnits="objectBoundingBox">
						<path d="M0.56 0C0.78 0.2 1 0.42 1 0.67C1 0.87 0.78 1 0.5 1C0.22 1 0 0.87 0 0.66C0 0.46 0.16 0.3 0.3 0.16C0.33 0.3 0.4 0.38 0.48 0.4C0.52 0.26 0.5 0.12 0.56 0Z"></path>
					</clipPath>
				</defs>
				<path d="M0 0h1440v40c-160 50-330 70-520 50-90-10-120-80-200-60-80 20-60 90-170 90C330 120 160 60 0 90z"></path>
			</svg>

			<div class="post-thumbnail">
				<?php if ( $photo_concept ) : ?>
					<img src="<?php echo esc_url( $photo_concept['url'] ); ?>" alt="<?php echo esc_attr( $photo_concept['alt'] ); ?>" width="<?php echo esc_attr( $photo_concept['width'] ); ?>" height="<?php echo esc_attr( $photo_concept['height'] ); ?>">
				<?php else : ?>
					<span aria-hidden="true">🔥</span>
				<?php endif; ?>
			</div>

			<div class="entry-content">
				<h2 class="entry-title" id="titre-concept"><?php echo esc_html( pepe_accueil( 'accueil_concept_titre' ) ); ?></h2>
				<?php echo wp_kses_post( pepe_accueil( 'accueil_concept_texte' ) ); ?>
				<a class="more-link" href="<?php echo esc_url( pepe_url_a_propos() ); ?>">Découvrir l’équipe →</a>
			</div>
		</article>

		<?php // 6. C'est validé ! : avis 5 étoiles (HTML des avis de WooCommerce), photos et sondage ?>
		<?php if ( $avis || $photos || $sondage ) : ?>
			<section class="pepe-valide" aria-labelledby="titre-valide">
				<h2 class="section-title" id="titre-valide">C’est validé !</h2>

				<ol class="commentlist">
					<?php
					// Les avis et les photos en alternance ; le dernier avis en rouge (classe alt, comme les boutons)
					$photos = $photos ? $photos : array();
					$nombre = max( count( $avis ), count( $photos ) );
					for ( $i = 0; $i < $nombre; $i++ ) {
						if ( isset( $avis[ $i ] ) ) {
							$alt = ( $i === count( $avis ) - 1 && $i > 0 );
							pepe_carte_avis_accueil( $avis[ $i ], $alt );
						}
						if ( isset( $photos[ $i ] ) ) {
							echo '<li>' . wp_get_attachment_image( $photos[ $i ]['ID'], 'medium_large' ) . '</li>';
						}
					}
					?>
				</ol>

				<?php if ( $sondage ) : ?>
					<div>
						<h3>Ton burger favori ?</h3>
						<ul class="pepe-sondage">
							<?php foreach ( $sondage as $nom => $pourcentage ) : ?>
								<li>
									<span style="width: <?php echo esc_attr( max( $pourcentage, 25 ) ); ?>%;">
										<?php echo esc_html( $nom ); ?> <strong><?php echo esc_html( $pourcentage ); ?> %</strong>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</section>
		<?php endif; ?>

	</main>
</div>

<?php
get_footer();
