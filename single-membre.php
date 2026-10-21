<?php
/**
 * Page d'un membre de l'équipe (IT-11 – US-11.3, US-11.4)
 * Photo = image mise en avant, présentation = contenu, le reste = champs SCF « Profil du membre »
 */

get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main pepe-profil">

		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$poste  = pepe_champ_membre( 'membre_poste' );
			$depuis = pepe_champ_membre( 'membre_depuis' );
			$plat   = pepe_champ_membre( 'membre_plat' );
			$pourquoi = pepe_champ_membre( 'membre_plat_texte' );
			?>

			<a class="pepe-profil__retour" href="<?php echo esc_url( get_post_type_archive_link( 'membre' ) . '#equipe' ); ?>">← Retour à l’équipe</a>

			<article class="pepe-profil__contenu">
				<?php pepe_photo_membre( 'large', 'pepe-profil__photo' ); ?>

				<div class="pepe-profil__infos">
					<?php if ( $poste ) : ?>
						<span class="pepe-badge"><?php echo esc_html( $poste ); ?></span>
					<?php endif; ?>

					<h1 class="pepe-profil__nom"><?php the_title(); ?></h1>

					<div class="pepe-profil__bio">
						<?php the_content(); ?>
					</div>

					<?php if ( $plat ) : ?>
						<div class="pepe-profil__plat">
							<h2 class="pepe-profil__plat-titre">
								Son plat préféré :
								<a href="<?php echo esc_url( get_permalink( $plat ) ); ?>"><?php echo esc_html( get_the_title( $plat ) ); ?></a>
							</h2>
							<?php if ( $pourquoi ) : ?>
								<p><?php echo esc_html( $pourquoi ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $depuis ) : ?>
						<p class="pepe-profil__depuis">Chez Pepe depuis <?php echo esc_html( $depuis ); ?></p>
					<?php endif; ?>
				</div>
			</article>
		<?php endwhile; ?>

	</main>
</div>

<?php
get_footer();
