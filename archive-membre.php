<?php
/**
 * Page « À propos » : le restaurant et la liste des membres de l'équipe (IT-11 – US-11.3, US-11.4)
 * Adresse : /equipe/ (archive du type de contenu « membre » créé dans SCF)
 */

get_header();

$ville   = get_option( 'woocommerce_store_city' );
$adresse = get_option( 'woocommerce_store_address' );
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main pepe-a-propos">

		<h1 class="pepe-a-propos__titre">À propos de Pepe Chicken</h1>

		<section class="pepe-a-propos__section" aria-labelledby="pepe-ou">
			<h2 class="pepe-a-propos__sous-titre" id="pepe-ou">Où nous trouver</h2>

			<div class="pepe-restaurant">
				<div class="pepe-restaurant__plan" aria-hidden="true">📍</div>
				<div class="pepe-restaurant__infos">
					<h3 class="pepe-restaurant__nom">Pepe Chicken <?php echo esc_html( $ville ? $ville : 'Limoges' ); ?></h3>
					<p><?php echo esc_html( $adresse ? $adresse . ', ' . $ville : 'Centre-ville de Limoges' ); ?></p>
					<p>Ouvert tous les jours · 11h30 – 22h30</p>
					<p class="pepe-restaurant__retrait">Click &amp; collect : commande en ligne, retrait au comptoir</p>
				</div>
			</div>
		</section>

		<section class="pepe-a-propos__section" id="equipe" aria-labelledby="pepe-equipe">
			<h2 class="pepe-a-propos__sous-titre" id="pepe-equipe">L’équipe</h2>
			<p class="pepe-a-propos__intro">Derrière chaque commande, une équipe passionnée. Clique sur un membre pour découvrir son profil.</p>

			<?php if ( have_posts() ) : ?>
				<ul class="pepe-equipe">
					<?php while ( have_posts() ) : the_post(); ?>
						<li class="pepe-membre">
							<a class="pepe-membre__lien" href="<?php the_permalink(); ?>">
								<?php pepe_photo_membre( 'medium_large', 'pepe-membre__photo' ); ?>
								<h3 class="pepe-membre__nom"><?php the_title(); ?></h3>
								<p class="pepe-membre__poste"><?php echo esc_html( pepe_champ_membre( 'membre_poste' ) ); ?></p>
								<span class="pepe-membre__voir">Voir le profil →</span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>
			<?php else : ?>
				<p class="pepe-a-propos__intro">L’équipe arrive bientôt.</p>
			<?php endif; ?>
		</section>

	</main>
</div>

<?php
get_footer();
