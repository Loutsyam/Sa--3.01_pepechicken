/**
 * Pepe Chicken – carrousel « Découvre nos classiques » (page d'accueil)
 * La flèche fait défiler la liste des catégories (ul.products de WooCommerce) d'une carte.
 */
document.querySelectorAll( '.storefront-product-categories' ).forEach( function ( bloc ) {
	var liste = bloc.querySelector( 'ul.products' );
	var fleche = bloc.querySelector( 'button' );

	if ( ! liste || ! fleche ) {
		return;
	}

	fleche.addEventListener( 'click', function () {
		var carte = liste.querySelector( 'li.product-category' );
		var pas = carte ? carte.offsetWidth + 24 : 300;

		// Au bout : on revient au début
		if ( liste.scrollLeft + liste.clientWidth >= liste.scrollWidth - 5 ) {
			liste.scrollTo( { left: 0, behavior: 'smooth' } );
		} else {
			liste.scrollBy( { left: pas, behavior: 'smooth' } );
		}
	} );
} );
