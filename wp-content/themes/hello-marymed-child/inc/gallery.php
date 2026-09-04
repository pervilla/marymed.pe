<?php
/**
 * Galeria de imagenes por ficha (sin plugins).
 *
 * Reune la imagen destacada + todos los adjuntos de imagen del post y los
 * muestra en una cuadricula con lightbox propio (ver assets/js/marymed-gallery.js).
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * IDs de imagen de un post: destacada primero + adjuntos hijos.
 *
 * @param int $post_id ID del post.
 * @return int[] IDs de attachment.
 */
function marymed_get_gallery_ids( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	$ids = array();
	if ( has_post_thumbnail( $post_id ) ) {
		$ids[] = (int) get_post_thumbnail_id( $post_id );
	}

	$children = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_parent'    => $post_id,
			'post_mime_type' => 'image',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'fields'         => 'ids',
			'numberposts'    => 30,
			'no_found_rows'  => true,
		)
	);

	foreach ( (array) $children as $id ) {
		$id = (int) $id;
		if ( ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * HTML de la cuadricula de galeria. '' si no hay imagenes.
 *
 * @param int  $post_id ID del post.
 * @param bool $echo    True imprime, false devuelve.
 * @return string
 */
function marymed_gallery_html( $post_id = 0, $echo = false ) {
	$ids = marymed_get_gallery_ids( $post_id );
	if ( empty( $ids ) ) {
		return '';
	}

	$html = '<div class="mm-gallery">';
	foreach ( $ids as $id ) {
		$full  = wp_get_attachment_image_url( $id, 'full' );
		$thumb = wp_get_attachment_image_url( $id, 'medium_large' );
		$alt   = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$alt   = $alt ? $alt : get_the_title( $post_id );

		if ( ! $full || ! $thumb ) {
			continue;
		}

		$html .= sprintf(
			'<button type="button" class="mm-gallery__item" data-full="%1$s" aria-label="%2$s"><img src="%3$s" alt="%2$s" loading="lazy"></button>',
			esc_url( $full ),
			esc_attr( $alt ),
			esc_url( $thumb )
		);
	}
	$html .= '</div>';

	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
		return '';
	}
	return $html;
}

/**
 * Recursos de galeria solo en singles de propiedades/vehiculos.
 */
function marymed_gallery_assets() {
	if ( ! is_singular( array( 'propiedades', 'vehiculos' ) ) ) {
		return;
	}

	wp_enqueue_script(
		'marymed-gallery-js',
		MARYMED_THEME_URI . '/assets/js/marymed-gallery.js',
		array(),
		MARYMED_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'marymed_gallery_assets', 30 );
