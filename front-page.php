<?php
/**
 * Page d'accueil « Le concept » (IT-2 – US-2.2), conforme à la maquette :
 * en-tête, nos classiques, nos nouveautés, produit à la une, le concept, « C'est validé ! »
 * Textes et images : groupe de champs SCF « Page d'accueil ». Fonctions : inc/accueil.php
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
	<main id="main" class="site-main pepe-accueil">

		<?php // 1. En-tête ?>
		<section class="pepe-une">
			<div class="pepe-une__texte">
				<h1 class="pepe-une__titre"><?php echo esc_html( pepe_accueil( 'accueil_titre' ) ); ?></h1>
				<p class="pepe-une__intro"><?php echo esc_html( pepe_accueil( 'accueil_texte' ) ); ?></p>
				<?php if ( pepe_accueil( 'accueil_petite_ligne' ) ) : ?>
					<p class="pepe-une__petit"><?php echo esc_html( pepe_accueil( 'accueil_petite_ligne' ) ); ?></p>
				<?php endif; ?>
				<a class="pepe-une__bouton" href="<?php echo esc_url( $lien_bouton ); ?>"><?php echo esc_html( pepe_accueil( 'accueil_bouton_texte' ) ); ?> ›</a>
			</div>
			<?php if ( $image ) : ?>
				<img class="pepe-une__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>">
			<?php endif; ?>
		</section>

		<?php // 2. Découvre nos classiques : une carte par catégorie ?>
		<section class="pepe-classiques" aria-labelledby="pepe-classiques-titre">
			<h2 class="pepe-accueil__titre" id="pepe-classiques-titre">Découvre nos classiques</h2>
			<div class="pepe-classiques__cadre">
				<ul class="pepe-classiques__liste">
					<?php foreach ( pepe_categories_produits() as $categorie ) : ?>
						<?php $vignette = get_term_meta( $categorie->term_id, 'thumbnail_id', true ); ?>
						<li class="pepe-classiques__carte">
							<a class="pepe-classiques__lien" href="<?php echo esc_url( get_term_link( $categorie ) ); ?>">
								<?php
								if ( $vignette ) {
									echo wp_get_attachment_image( $vignette, 'woocommerce_thumbnail', false, array( 'class' => 'pepe-classiques__image' ) );
								} else {
									echo wc_placeholder_img( 'woocommerce_thumbnail', array( 'class' => 'pepe-classiques__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
								<span class="pepe-classiques__nom"><?php echo esc_html( $categorie->name ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<button type="button" class="pepe-classiques__suivant" aria-label="Catégories suivantes">›</button>
			</div>
			<p class="pepe-accueil__action">
				<a class="pepe-accueil__bouton" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Voir toute la carte et commander</a>
			</p>
		</section>

		<?php // 3. Nos nouveautés : mêmes cartes que la Boutique ?>
		<section class="pepe-nouveautes" aria-labelledby="pepe-nouveautes-titre">
			<h2 class="pepe-accueil__titre" id="pepe-nouveautes-titre">Nos nouveautés</h2>
			<?php
			$ids = pepe_nouveautes();
			if ( $ids ) {
				echo do_shortcode( '[products ids="' . esc_attr( implode( ',', $ids ) ) . '" columns="3" orderby="post__in"]' );
			}
			?>
		</section>

		<?php // 4. Produit à la une ?>
		<?php if ( $vedette ) : ?>
			<section class="pepe-vedette" aria-labelledby="pepe-vedette-titre">
				<div class="pepe-vedette__visuel">
					<span class="pepe-vedette__numero" aria-hidden="true">#1</span>
					<?php echo $vedette->get_image( 'woocommerce_single', array( 'class' => 'pepe-vedette__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="pepe-vedette__infos">
					<?php if ( pepe_accueil( 'accueil_vedette_badge' ) ) : ?>
						<span class="pepe-badge"><?php echo esc_html( pepe_accueil( 'accueil_vedette_badge' ) ); ?></span>
					<?php endif; ?>
					<h2 class="pepe-vedette__nom" id="pepe-vedette-titre"><?php echo esc_html( $vedette->get_name() ); ?></h2>
					<p class="pepe-vedette__description"><?php echo esc_html( wp_strip_all_tags( $vedette->get_short_description() ? $vedette->get_short_description() : $vedette->get_description() ) ); ?></p>
					<p class="pepe-vedette__achat">
						<span class="pepe-vedette__prix"><?php echo wp_kses_post( $vedette->get_price_html() ); ?></span>
						<a class="pepe-vedette__bouton" href="<?php echo esc_url( $vedette->get_permalink() ); ?>">Commander</a>
					</p>
				</div>
			</section>
		<?php endif; ?>

		<?php // 5. Le concept (fond beige, vague en haut, photo en forme de flamme) ?>
		<section class="pepe-concept" id="concept" aria-labelledby="pepe-concept-titre">
			<svg class="pepe-concept__vague" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true" focusable="false">
				<path d="M0 0h1440v40c-160 50-330 70-520 50-90-10-120-80-200-60-80 20-60 90-170 90C330 120 160 60 0 90z"></path>
			</svg>
			<svg class="pepe-concept__forme" aria-hidden="true" focusable="false">
				<clipPath id="pepe-flamme" clipPathUnits="objectBoundingBox">
					<path d="M0.56 0C0.78 0.2 1 0.42 1 0.67C1 0.87 0.78 1 0.5 1C0.22 1 0 0.87 0 0.66C0 0.46 0.16 0.3 0.3 0.16C0.33 0.3 0.4 0.38 0.48 0.4C0.52 0.26 0.5 0.12 0.56 0Z"></path>
				</clipPath>
			</svg>
			<div class="pepe-concept__contenu">
				<?php if ( $photo_concept ) : ?>
					<img class="pepe-concept__photo" src="<?php echo esc_url( $photo_concept['url'] ); ?>" alt="<?php echo esc_attr( $photo_concept['alt'] ); ?>" width="<?php echo esc_attr( $photo_concept['width'] ); ?>" height="<?php echo esc_attr( $photo_concept['height'] ); ?>">
				<?php else : ?>
					<span class="pepe-concept__photo pepe-concept__photo--vide" aria-hidden="true">🔥</span>
				<?php endif; ?>
				<div class="pepe-concept__texte">
					<h2 class="pepe-concept__titre" id="pepe-concept-titre"><?php echo esc_html( pepe_accueil( 'accueil_concept_titre' ) ); ?></h2>
					<?php echo wp_kses_post( pepe_accueil( 'accueil_concept_texte' ) ); ?>
					<a class="pepe-concept__lien" href="<?php echo esc_url( pepe_url_a_propos() ); ?>">Découvrir l’équipe →</a>
				</div>
			</div>
		</section>

		<?php // 6. C'est validé ! : avis 5 étoiles, photos des clients et sondage ?>
		<?php if ( $avis || $photos || $sondage ) : ?>
			<section class="pepe-valide" aria-labelledby="pepe-valide-titre">
				<h2 class="pepe-valide__titre" id="pepe-valide-titre">C’est validé !</h2>
				<div class="pepe-valide__grille">
					<?php
					// Les avis et les photos en alternance, le dernier avis en rouge
					$photos = $photos ? $photos : array();
					$nombre = max( count( $avis ), count( $photos ) );
					for ( $i = 0; $i < $nombre; $i++ ) {
						if ( isset( $avis[ $i ] ) ) {
							$classe = ( $i === count( $avis ) - 1 && $i > 0 ) ? 'pepe-valide__avis--rouge' : '';
							pepe_carte_avis_accueil( $avis[ $i ], $classe );
						}
						if ( isset( $photos[ $i ] ) ) {
							echo wp_get_attachment_image( $photos[ $i ]['ID'], 'medium_large', false, array( 'class' => 'pepe-valide__photo' ) );
						}
					}
					?>

					<?php if ( $sondage ) : ?>
						<div class="pepe-valide__sondage">
							<h3 class="pepe-valide__question">Ton burger favori ?</h3>
							<ul class="pepe-sondage">
								<?php foreach ( $sondage as $nom => $pourcentage ) : ?>
									<li class="pepe-sondage__ligne">
										<span class="pepe-sondage__barre" style="width: <?php echo esc_attr( max( $pourcentage, 25 ) ); ?>%;">
											<span class="pepe-sondage__nom"><?php echo esc_html( $nom ); ?></span>
											<span class="pepe-sondage__valeur"><?php echo esc_html( $pourcentage ); ?> %</span>
										</span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

	</main>
</div>

<?php
get_footer();
