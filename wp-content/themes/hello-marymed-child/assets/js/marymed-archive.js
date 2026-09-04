/**
 * Marymed Real Estate - Filtrado AJAX de listados.
 *
 * Intercepta el submit del formulario .mm-filters y los clicks de
 * .mm-pagination .page-numbers para re-renderizar el contenedor
 * [data-mm-results] sin recargar la pagina. Si JS falla, el form
 * conserva su accion GET normal (progressive enhancement).
 */
( function () {
	'use strict';

	var cfg = window.MARYMED_AJAX || { url: '', cpt: '' };
	if ( ! cfg.url ) {
		return;
	}

	/**
	 * Serializa el formulario en objeto de filtros.
	 */
	function filtersFrom( form ) {
		var data = { action: 'marymed_ajax_archive', post_type: cfg.cpt, paged: 1 };
		if ( ! form ) {
			return data;
		}
		var fd = new FormData( form );
		fd.forEach( function ( value, key ) {
			data[ key ] = value;
		} );
		return data;
	}

	/**
	 * Pide el HTML de resultados y lo inyecta.
	 */
	function loadResults( params ) {
		var results = document.querySelector( '[data-mm-results]' );
		if ( ! results ) {
			return;
		}

		results.classList.add( 'is-loading' );
		results.style.opacity = '0.5';

		var body = Object.keys( params )
			.map( function ( k ) {
				return encodeURIComponent( k ) + '=' + encodeURIComponent( params[ k ] );
			} )
			.join( '&' );

		fetch( cfg.url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body,
		} )
			.then( function ( r ) {
				return r.json();
			} )
			.then( function ( res ) {
				results.style.opacity = '';
				results.classList.remove( 'is-loading' );
				if ( res && res.success && res.data && res.data.html ) {
					results.innerHTML = res.data.html;
					window.scrollTo( { top: results.getBoundingClientRect().top + window.scrollY - 80, behavior: 'smooth' } );
				}
			} )
			.catch( function () {
				results.style.opacity = '';
				results.classList.remove( 'is-loading' );
			} );
	}

	/**
	 * Refleja los filtros activos en la URL (compartible).
	 */
	function replaceState( params ) {
		if ( ! window.history || ! window.history.replaceState ) {
			return;
		}
		var qs = Object.keys( params )
			.filter( function ( k ) {
				return k !== 'action' && k !== 'post_type' && params[ k ] !== '' && params[ k ] !== 1 && k !== 'paged';
			} )
			.map( function ( k ) {
				return encodeURIComponent( k ) + '=' + encodeURIComponent( params[ k ] );
			} )
			.join( '&' );
		var url = window.location.pathname + ( qs ? '?' + qs : '' );
		window.history.replaceState( {}, '', url );
	}

	function pageFromHref( href ) {
		var m = href.match( /[?&]paged=(\d+)/ );
		return m ? parseInt( m[ 1 ], 10 ) : 1;
	}

	// Submit del formulario de filtros.
	document.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		if ( ! form.classList || ! form.classList.contains( 'mm-filters' ) ) {
			return;
		}
		e.preventDefault();
		var params = filtersFrom( form );
		replaceState( params );
		loadResults( params );
	} );

	// Clicks en la paginacion (delegacion sobre el documento).
	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest ? e.target.closest( '.mm-pagination .page-numbers' ) : null;
		if ( ! link ) {
			return;
		}
		var form = document.querySelector( '.mm-filters' );
		var params = filtersFrom( form );
		params.paged = pageFromHref( link.getAttribute( 'href' ) || '' );
		e.preventDefault();
		replaceState( params );
		loadResults( params );
	} );
} )();
