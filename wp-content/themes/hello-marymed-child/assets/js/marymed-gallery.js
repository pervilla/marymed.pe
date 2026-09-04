/**
 * Marymed Real Estate - Lightbox para .mm-gallery__item.
 *
 * Crea el overlay una sola vez y permite navegar entre las fotos con
 * flechas / teclado / toque. Sin dependencias.
 */
( function () {
	'use strict';

	var items = Array.prototype.slice.call( document.querySelectorAll( '.mm-gallery__item' ) );
	if ( ! items.length ) {
		return;
	}

	var overlay = document.createElement( 'div' );
	overlay.className = 'mm-lightbox';
	overlay.setAttribute( 'role', 'dialog' );
	overlay.setAttribute( 'aria-modal', 'true' );

	var img = document.createElement( 'img' );
	var cap = document.createElement( 'div' );
	cap.className = 'mm-lightbox__caption';

	var close = document.createElement( 'button' );
	close.type = 'button';
	close.className = 'mm-lightbox__btn mm-lightbox__close';
	close.innerHTML = '&times;';
	close.setAttribute( 'aria-label', 'Cerrar' );

	var prev = document.createElement( 'button' );
	prev.type = 'button';
	prev.className = 'mm-lightbox__btn mm-lightbox__prev';
	prev.innerHTML = '&lsaquo;';
	prev.setAttribute( 'aria-label', 'Anterior' );

	var next = document.createElement( 'button' );
	next.type = 'button';
	next.className = 'mm-lightbox__btn mm-lightbox__next';
	next.innerHTML = '&rsaquo;';
	next.setAttribute( 'aria-label', 'Siguiente' );

	overlay.appendChild( close );
	overlay.appendChild( prev );
	overlay.appendChild( img );
	overlay.appendChild( cap );
	overlay.appendChild( next );
	document.body.appendChild( overlay );

	var index = 0;

	function current() {
		return items[ index ];
	}

	function show( i ) {
		if ( i < 0 ) {
			i = items.length - 1;
		}
		if ( i >= items.length ) {
			i = 0;
		}
		index = i;
		var it = current();
		img.src = it.getAttribute( 'data-full' );
		cap.textContent = it.getAttribute( 'aria-label' ) || '';
		overlay.classList.add( 'is-open' );
		document.body.style.overflow = 'hidden';
		prev.style.display = items.length > 1 ? '' : 'none';
		next.style.display = items.length > 1 ? '' : 'none';
	}

	function closeLightbox() {
		overlay.classList.remove( 'is-open' );
		document.body.style.overflow = '';
	}

	items.forEach( function ( item, i ) {
		item.addEventListener( 'click', function () {
			show( i );
		} );
	} );

	close.addEventListener( 'click', closeLightbox );
	overlay.addEventListener( 'click', function ( e ) {
		if ( e.target === overlay ) {
			closeLightbox();
		}
	} );
	prev.addEventListener( 'click', function ( e ) {
		e.stopPropagation();
		show( index - 1 );
	} );
	next.addEventListener( 'click', function ( e ) {
		e.stopPropagation();
		show( index + 1 );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( ! overlay.classList.contains( 'is-open' ) ) {
			return;
		}
		if ( e.key === 'Escape' ) {
			closeLightbox();
		}
		if ( e.key === 'ArrowLeft' ) {
			show( index - 1 );
		}
		if ( e.key === 'ArrowRight' ) {
			show( index + 1 );
		}
	} );
} )();
