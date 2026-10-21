/**
 * Pepe Chicken – carrousel « Découvre nos classiques » (page d'accueil)
 * La flèche fait défiler la liste d'une carte.
 */
document.querySelectorAll( '.pepe-classiques' ).forEach( function ( bloc ) {
	var liste = bloc.querySelector( '.pepe-classiques__liste' );
	var fleche = bloc.querySelector( '.pepe-classiques__suivant' );

	if ( ! liste || ! fleche ) {
		return;
	}

	fleche.addEventListener( 'click', function () {
		var carte = liste.querySelector( '.pepe-classiques__carte' );
		var pas = carte ? carte.offsetWidth + 24 : 300;

		// Au bout : on revient au début
		if ( liste.scrollLeft + liste.clientWidth >= liste.scrollWidth - 5 ) {
			liste.scrollTo( { left: 0, behavior: 'smooth' } );
		} else {
			liste.scrollBy( { left: pas, behavior: 'smooth' } );
		}
	} );
} );
