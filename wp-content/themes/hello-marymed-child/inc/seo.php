<?php
/**
 * SEO basico + Open Graph (WhatsApp/Facebook/Twitter) y descripciones.
 *
 * El sitemap.xml lo genera el nucleo de WP (/wp-sitemap.xml) e incluye
 * los CPT automaticamente por show_in_rest/public.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Descripcion meta para una publicacion.
 */
function marymed_meta_description( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$desc = get_the_excerpt( $post_id );
	if ( ! $desc ) {
		$content = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
		$desc    = wp_trim_words( $content, 28, '' );
	}

	$desc = wp_html_excerpt( $desc, 155, '' );
	return trim( $desc );
}

/**
 * <meta name="description"> en singulares y archivos de los CPT.
 */
function marymed_meta_description_tag() {
	if ( is_singular() ) {
		$desc = marymed_meta_description();
	} elseif ( is_post_type_archive( 'propiedades' ) ) {
		$desc = __( 'Lotes, casas, departamentos y edificios en venta y alquiler en Peru.', 'marymed' );
	} elseif ( is_post_type_archive( 'vehiculos' ) ) {
		$desc = __( 'Autos, camionetas, motos y mas en venta. Fichas tecnicas con video.', 'marymed' );
	} else {
		$desc = get_bloginfo( 'description' );
	}

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
}
add_action( 'wp_head', 'marymed_meta_description_tag', 3 );

/**
 * Open Graph + Twitter Cards para compartir bonito en WhatsApp/redes.
 */
function marymed_og_meta() {
	if ( is_singular() ) {
		$post_id    = get_the_ID();
		$title      = get_the_title( $post_id );
		$url        = get_permalink( $post_id );
		$type       = 'article';
		$desc       = marymed_meta_description( $post_id );
		$image      = get_the_post_thumbnail_url( $post_id, 'large' );

		if ( ! $image && function_exists( 'marymed_get_gallery_ids' ) ) {
			$ids = marymed_get_gallery_ids( $post_id );
			if ( $ids ) {
				$image = wp_get_attachment_image_url( $ids[0], 'large' );
			}
		}
	} else {
		$post_id = 0;
		$title   = wp_get_document_title();
		$url     = home_url( '/' );
		$type    = 'website';
		$desc    = get_bloginfo( 'description' );
		$image   = '';
	}

	$site_name = get_bloginfo( 'name' );

	echo "\n<!-- Open Graph / Marymed -->\n";
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:locale" content="es_PE">' . "\n" );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	}
}
add_action( 'wp_head', 'marymed_og_meta', 4 );
