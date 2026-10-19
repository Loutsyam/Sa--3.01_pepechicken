/**
 * Pepe Chicken – Boutons − / + de la page Panier (IT-5, US-5.3)
 *
 * Un clic change la quantité puis clique sur le bouton caché
 * « Mettre à jour le panier » : le script du panier de WooCommerce
 * recharge les articles et le récapitulatif sans recharger la page.
 */
jQuery( function ( $ ) {
	var minuteur = null;

	$( document.body ).on( 'click', '.pepe-quantite__bouton', function () {
		var $champ = $( this ).closest( '.pepe-article__quantite' ).find( 'input.qty' );
		var quantite = parseInt( $champ.val(), 10 ) || 0;
		var nouvelle = Math.max( 0, quantite + parseInt( $( this ).data( 'pepe-quantite' ), 10 ) );
		var max = parseInt( $champ.attr( 'max' ), 10 );

		if ( max > 0 && nouvelle > max ) {
			nouvelle = max;
		}

		$champ.val( nouvelle ).trigger( 'change' );

		// On attend un peu si on clique plusieurs fois de suite
		clearTimeout( minuteur );
		minuteur = setTimeout( function () {
			$( '.pepe-panier__maj' ).prop( 'disabled', false ).trigger( 'click' );
		}, 500 );
	} );

	// Après une mise à jour, on recompte les articles du titre
	$( document.body ).on( 'updated_wc_div', function () {
		var total = 0;
		$( '.pepe-article input.qty' ).each( function () {
			total += parseInt( $( this ).val(), 10 ) || 0;
		} );
		$( '.pepe-panier__nombre' ).text( total + ( total > 1 ? ' articles' : ' article' ) );
	} );
} );
