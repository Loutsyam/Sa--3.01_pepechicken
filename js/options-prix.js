/**
 * Pepe Chicken – Prix de la fiche produit selon les options (IT-3)
 *
 * Prix affiché = prix du produit (ou de la variation choisie)
 *              + suppléments des options cochées.
 * Une option cachée (accompagnement sans la formule Menu) ne compte pas.
 *
 * Variations (US-3.1) : les pilules « Burger seul / Menu » choisissent la
 * valeur dans la liste déroulante cachée de WooCommerce, qui trouve alors
 * la variation, son prix et son stock.
 */
jQuery( function ( $ ) {
	var $formulaire = $( 'form.cart' );
	var $liste = $( '.pepe-choix-liste' );
	var $prix = $( '.summary > .price' ).first();
	var prixDeDepart = $prix.html();

	/* 1. Pilules des variations */

	// Met à jour l'aspect des pilules d'une liste déroulante
	function mettreAJourPilules( $select ) {
		var valeur = $select.val();

		$select.siblings( '.pepe-options' ).find( '.pepe-option--variation' ).each( function () {
			var actif = String( $( this ).data( 'valeur' ) ) === valeur;
			$( this ).toggleClass( 'pepe-option--actif', actif ).attr( 'aria-pressed', actif ? 'true' : 'false' );
		} );
	}

	// Une valeur sans aucune variation en stock : pilule « épuisé »
	function marquerEpuises() {
		var variations = $formulaire.data( 'product_variations' ) || [];

		$formulaire.find( '.pepe-option--variation' ).each( function () {
			var $pilule = $( this );
			var nom = $pilule.closest( '.pepe-options' ).siblings( 'select' ).attr( 'name' );
			var valeur = String( $pilule.data( 'valeur' ) );
			var enStock = variations.some( function ( variation ) {
				return variation.attributes[ nom ] === valeur && variation.is_in_stock;
			} );

			$pilule.toggleClass( 'pepe-option--epuise', ! enStock ).prop( 'disabled', ! enStock );
			if ( ! enStock ) {
				$pilule.attr( 'title', 'Épuisé' );
			}
		} );
	}

	$formulaire.on( 'click', '.pepe-option--variation', function () {
		var $select = $( this ).closest( '.pepe-options' ).siblings( 'select' );
		$select.val( String( $( this ).data( 'valeur' ) ) ).trigger( 'change' );
	} );

	$formulaire.on( 'change', '.variations select', function () {
		mettreAJourPilules( $( this ) );
	} );

	$formulaire.find( '.variations select' ).each( function () {
		mettreAJourPilules( $( this ) );
	} );
	marquerEpuises();

	/* 2. Prix et groupes du menu */

	if ( ! $liste.length || ! $prix.length ) {
		return;
	}

	var prixDeBase = parseFloat( $liste.data( 'prix' ) );
	var decimales = parseInt( $liste.data( 'decimales' ), 10 );
	var separateur = String( $liste.data( 'separateur' ) );
	var devise = String( $liste.data( 'devise' ) );
	var variationChoisie = ! $formulaire.hasClass( 'variations_form' );

	// Les lecteurs d'écran annoncent le nouveau prix
	$prix.attr( 'aria-live', 'polite' );

	// 15.4 => « 15,40 € »
	function formaterPrix( prix ) {
		return prix.toFixed( decimales ).replace( '.', separateur ) + ' ' + devise;
	}

	function calculerPrix() {
		// Produit variable sans variation choisie : on garde la fourchette de prix
		if ( ! variationChoisie ) {
			$prix.html( prixDeDepart );
			return;
		}

		var total = prixDeBase;

		$liste.find( '.pepe-option__input:checked' ).each( function () {
			// Groupe caché (display: none) : on ne compte pas
			if ( $( this ).closest( '.pepe-choix' ).is( ':visible' ) ) {
				total = total + parseFloat( $( this ).data( 'prix' ) );
			}
		} );

		$prix.text( formaterPrix( total ) );
	}

	// Variation trouvée : son prix devient le prix de base
	$formulaire.on( 'found_variation', function ( evenement, variation ) {
		prixDeBase = parseFloat( variation.display_price );
		variationChoisie = true;
		$liste.toggleClass( 'pepe-choix-liste--menu', variation.attributes.attribute_formule === 'Menu' );
		calculerPrix();
	} );

	$formulaire.on( 'reset_data', function () {
		variationChoisie = false;
		$liste.removeClass( 'pepe-choix-liste--menu' );
		calculerPrix();
	} );

	$liste.on( 'change', calculerPrix );
	calculerPrix();
} );
