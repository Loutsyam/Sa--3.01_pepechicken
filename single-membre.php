<?php
/**
 * Page d'un membre de l'équipe (IT-11 – US-11.3, US-11.4)
 * Photo = image mise en avant, présentation = contenu, le reste = champs SCF « Profil du membre »
 *
 * HTML d'un article de Storefront : article hentry (post_class), post-thumbnail,
 * entry-header / entry-title, entry-content. Le poste est dans un mark (pastille jaune).
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$poste    = pepe_champ_membre( 'membre_poste' );
			$depuis   = pepe_champ_membre( 'membre_depuis' );
			$plat     = pepe_champ_membre( 'membre_plat' );
			$pourquoi = pepe_champ_membre( 'membre_plat_texte' );
			?>

			<p><a href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) . '#equipe' ); ?>">← Retour à l’équipe</a></p>

			<article <?php post_class(); ?>>
				<?php pepe_photo_membre( 'large' ); ?>

				<div class="entry-content">
					<header class="entry-header">
						<?php if ( $poste ) : ?>
							<mark><?php echo esc_html( $poste ); ?></mark>
						<?php endif; ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<?php the_content(); ?>

					<?php if ( $plat ) : ?>
						<aside>
							<h2>
								Son plat préféré :
								<a href="<?php echo esc_url( get_permalink( $plat ) ); ?>"><?php echo esc_html( get_the_title( $plat ) ); ?></a>
							</h2>
							<?php if ( $pourquoi ) : ?>
								<p><?php echo esc_html( $pourquoi ); ?></p>
							<?php endif; ?>
						</aside>
					<?php endif; ?>

					<?php if ( $depuis ) : ?>
						<p><small>Chez Pepe depuis <?php echo esc_html( $depuis ); ?></small></p>
					<?php endif; ?>
				</div>
			</article>
		<?php endwhile; ?>

	</main>
</div>

<?php
get_footer();
