<?php
/**
 * IT-11 : page « À propos » et membres de l'équipe
 *
 * US-11.1 : type de contenu « Équipe » (membre) et groupe de champs « Profil du membre »
 *           créés dans SCF (acf-json/), page de l'équipe = archive /equipe/
 * US-11.3 : templates archive-membre.php (liste) et single-membre.php (un membre)
 * US-11.5 : lien « À propos » du header et du footer vers la page de l'équipe
 */

defined( 'ABSPATH' ) || exit;

// Page de l'équipe : tous les membres, dans l'ordre choisi (champ « Ordre » de chaque membre)
function pepe_ordre_equipe( $requete ) {
    if ( ! is_admin() && $requete->is_main_query() && $requete->is_post_type_archive( 'membre' ) ) {
        $requete->set( 'posts_per_page', -1 );
        $requete->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
    }
}
add_action( 'pre_get_posts', 'pepe_ordre_equipe' );

// Titre de l'onglet du navigateur
function pepe_titre_page_equipe( $titre ) {
    if ( is_post_type_archive( 'membre' ) ) {
        $titre['title'] = 'À propos';
    }
    return $titre;
}
add_filter( 'document_title_parts', 'pepe_titre_page_equipe' );

// Champ SCF d'un membre (texte vide si SCF n'est pas activé)
function pepe_champ_membre( $nom, $membre_id = null ) {
    if ( ! function_exists( 'get_field' ) ) {
        return '';
    }
    return get_field( $nom, $membre_id ? $membre_id : get_the_ID() );
}

// Photo du membre (div.post-thumbnail comme Storefront), ou silhouette si aucune photo n'est choisie
function pepe_photo_membre( $taille ) {
    echo '<div class="post-thumbnail">';
    if ( has_post_thumbnail() ) {
        the_post_thumbnail( $taille );
    } else {
        echo '<span aria-hidden="true">👤</span>';
    }
    echo '</div>';
}

// Fil d'Ariane : « À propos » au lieu du nom du type de contenu
function pepe_ariane_equipe( $miettes ) {
    if ( ! is_post_type_archive( 'membre' ) && ! is_singular( 'membre' ) ) {
        return $miettes;
    }

    foreach ( $miettes as $i => $miette ) {
        if ( in_array( $miette[0], array( 'Équipe', 'Membre' ), true ) ) {
            $miettes[ $i ][0] = 'À propos';
            $miettes[ $i ][1] = get_post_type_archive_link( 'membre' );
        }
    }
    return $miettes;
}
add_filter( 'woocommerce_get_breadcrumb', 'pepe_ariane_equipe' );
