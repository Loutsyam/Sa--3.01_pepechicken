<?php
/**
 * Page « À propos » : le restaurant et la liste des membres de l'équipe (IT-11 – US-11.3, US-11.4)
 * Adresse : /equipe/ (archive du type de contenu « membre » créé dans SCF)
 *
 * HTML d'une archive de Storefront : page-header / page-title, un article hentry par membre
 * (post_class), post-thumbnail, entry-header, entry-title, more-link.
 */

get_header();

$ville   = get_option( 'woocommerce_store_city' );
$adresse = get_option( 'woocommerce_store_address' );
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<header class="page-header">
			<h1 class="page-title">À propos de Pepe Chicken</h1>
		</header>

		<section aria-labelledby="titre-ou">
			<h2 class="section-title" id="titre-ou">Où nous trouver</h2>

			<div class="hentry">
				<div class="post-thumbnail" aria-hidden="true">📍</div>
				<div class="entry-content">
					<h3 class="entry-title">Pepe Chicken <?php echo esc_html( $ville ? $ville : 'Limoges' ); ?></h3>
					<address><?php echo esc_html( $adresse ? $adresse . ', ' . $ville : 'Centre-ville de Limoges' ); ?></address>
					<p>Ouvert tous les jours · 11h30 – 22h30</p>
					<p><strong>Click &amp; collect : commande en ligne, retrait au comptoir</strong></p>
				</div>
			</div>
		</section>

		<section id="equipe" aria-labelledby="titre-equipe">
			<h2 class="section-title" id="titre-equipe">L’équipe</h2>
			<p>Derrière chaque commande, une équipe passionnée. Clique sur un membre pour découvrir son profil.</p>

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class(); ?>>
						<a href="<?php the_permalink(); ?>">
							<?php pepe_photo_membre( 'medium_large' ); ?>
							<header class="entry-header">
								<h3 class="entry-title"><?php the_title(); ?></h3>
								<p><?php echo esc_html( pepe_champ_membre( 'membre_poste' ) ); ?></p>
							</header>
							<span class="more-link">Voir le profil →</span>
						</a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p>L’équipe arrive bientôt.</p>
			<?php endif; ?>
		</section>

	</main>
</div>

<?php
get_footer();
