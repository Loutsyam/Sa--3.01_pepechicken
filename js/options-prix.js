/**
 * Pepe Chicken – Prix de la fiche produit selon les options (IT-3, US-3.3)
 *
 * Prix affiché = prix du produit + suppléments des options cochées.
 * Une option cachée (accompagnement sans la formule Menu) ne compte pas.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	var liste = document.querySelector( '.pepe-choix-liste' );
	var prixAffiche = document.querySelector( '.summary .price' );

	if ( ! liste || ! prixAffiche ) {
		return;
	}

	var prixDeBase = parseFloat( liste.dataset.prix );
	var decimales = parseInt( liste.dataset.decimales, 10 );
	var separateur = liste.dataset.separateur;
	var devise = liste.dataset.devise;

	// Les lecteurs d'écran annoncent le nouveau prix
	prixAffiche.setAttribute( 'aria-live', 'polite' );

	// 15.4 => « 15,40 € »
	function formaterPrix( prix ) {
		return prix.toFixed( decimales ).replace( '.', separateur ) + ' ' + devise;
	}

	function calculerPrix() {
		var total = prixDeBase;
		var cochees = liste.querySelectorAll( '.pepe-option__input:checked' );

		cochees.forEach( function ( option ) {
			var groupe = option.closest( '.pepe-choix' );

			// Groupe caché en CSS (display: none) : on ne compte pas
			if ( groupe.offsetParent !== null ) {
				total = total + parseFloat( option.dataset.prix );
			}
		} );

		prixAffiche.textContent = formaterPrix( total );
	}

	liste.addEventListener( 'change', calculerPrix );
	calculerPrix();
} );
