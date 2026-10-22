/**
 * Pepe Chicken – Boutons − / + de la page Panier (IT-5, US-5.3)
 *
 * Un clic change la quantité puis clique sur le bouton caché
 * « Mettre à jour le panier » : le script du panier de WooCommerce
 * recharge les articles et le récapitulatif sans recharger la page.
 */
jQuery( function ( $ ) {
	var minuteur = null;

	$( document.body ).on( 'click', '.product-quantity .wc-block-components-quantity-selector__button', function () {
		var $champ = $( this ).closest( '.product-quantity' ).find( 'input.qty' );
		var quantite = parseInt( $champ.val(), 10 ) || 0;
		var pas = $( this ).hasClass( 'wc-block-components-quantity-selector__button--minus' ) ? -1 : 1;
		var nouvelle = Math.max( 0, quantite + pas );
		var max = parseInt( $champ.attr( 'max' ), 10 );

		if ( max > 0 && nouvelle > max ) {
			nouvelle = max;
		}

		$champ.val( nouvelle ).trigger( 'change' );

		// On attend un peu si on clique plusieurs fois de suite
		clearTimeout( minuteur );
		minuteur = setTimeout( function () {
			$( '.woocommerce-cart-form [name="update_cart"]' ).prop( 'disabled', false ).trigger( 'click' );
		}, 500 );
	} );

	// Après une mise à jour, on recompte les articles affichés dans le titre « Mon panier »
	$( document.body ).on( 'updated_wc_div', function () {
		var total = 0;
		$( '.woocommerce-cart-form input.qty' ).each( function () {
			total += parseInt( $( this ).val(), 10 ) || 0;
		} );
		$( '.entry-title small' ).text( total + ( total > 1 ? ' articles' : ' article' ) );
	} );
} );
