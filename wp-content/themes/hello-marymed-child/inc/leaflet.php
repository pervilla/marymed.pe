<?php
/**
 * Mapa gratuito Leaflet + OpenStreetMap (sin API key ni tarjeta).
 * Lee lat/lng/zoom del grupo ACF `ubicacion_mapbox`.
 *
 * Reemplaza a Mapbox manteniendo la misma estructura de datos, de modo que
 * migrar a Mapbox en el futuro solo implica cambiar este modulo.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Datos de ubicacion de un post. ACF guarda los subcampos de grupo en
 * metakeys planas (ubicacion_mapbox_lat, ...); tambien soporta el caso
 * de guardado como arreglo (edicion por UI de ACF).
 *
 * @param int $post_id ID del post.
 * @return array Claves: lat, lng, zoom, zona, direccion.
 */
function marymed_location_data( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$group = get_field( 'ubicacion_mapbox', $post_id );
	if ( is_array( $group ) && isset( $group['lat'] ) ) {
		return wp_parse_args(
			$group,
			array( 'lat' => '', 'lng' => '', 'zoom' => 15, 'zona' => '', 'direccion' => '' )
		);
	}

	return array(
		'lat'       => get_field( 'ubicacion_mapbox_lat', $post_id ),
		'lng'       => get_field( 'ubicacion_mapbox_lng', $post_id ),
		'zoom'      => get_field( 'ubicacion_mapbox_zoom', $post_id ),
		'zona'      => get_field( 'ubicacion_mapbox_zona', $post_id ),
		'direccion' => get_field( 'ubicacion_mapbox_direccion', $post_id ),
	);
}

/**
 * Render del mapa Leaflet autocontenido.
 *
 * @param int  $post_id ID del post.
 * @param bool $echo    True imprime, false devuelve.
 * @return string HTML o '' si faltan coordenadas.
 */
function marymed_leaflet_map( $post_id = 0, $echo = true ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! function_exists( 'get_field' ) || ! $post_id ) {
		return '';
	}

	$location = marymed_location_data( $post_id );
	$lat      = isset( $location['lat'] ) ? (float) $location['lat'] : 0;
	$lng      = isset( $location['lng'] ) ? (float) $location['lng'] : 0;

	if ( ! $lat || ! $lng ) {
		return '';
	}

	$zoom   = ! empty( $location['zoom'] ) ? (float) $location['zoom'] : 15;
	$etiqueta = get_the_title( $post_id );
	$id     = 'marymed-map-' . absint( $post_id );

	$out  = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>';
	$out .= '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>';
	$out .= '<div id="' . esc_attr( $id ) . '" class="mm-leaflet"></div>';
	$out .= '<script>'
		. '(function(){'
		. 'var el=document.getElementById(' . wp_json_encode( $id ) . ');'
		. 'var map=L.map(el).setView([' . $lat . ',' . $lng . '],' . $zoom . ');'
		. 'L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png",{maxZoom:19,attribution:"&copy; OpenStreetMap"}).addTo(map);'
		. 'L.marker([' . $lat . ',' . $lng . ']).addTo(map).bindPopup(' . wp_json_encode( $etiqueta ) . ').openPopup();'
		. 'setTimeout(function(){map.invalidateSize();},250);'
		. '})();'
		. '</script>';

	if ( $echo ) {
		echo $out; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML y script Leaflet.
		return '';
	}
	return $out;
}
add_shortcode( 'marymed_mapa', 'marymed_leaflet_map' );

/**
 * Inyecta el codigo de recorrido 3D (iframe de Google Earth Studio/YouTube)
 * guardado en el campo ACF `codigo_3d_recorrido`. '' si vacio.
 *
 * @param int $post_id ID del post.
 * @return string Contenido embebido o ''.
 */
function marymed_recorrido_3d( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$html = ( function_exists( 'get_field' ) ) ? get_field( 'codigo_3d_recorrido', $post_id ) : '';

	if ( empty( $html ) ) {
		return '';
	}
	return '<div class="mm-3d">' . $html . '</div>';
}
add_shortcode( 'marymed_recorrido_3d', 'marymed_recorrido_3d' );
