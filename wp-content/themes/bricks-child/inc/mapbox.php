<?php
/**
 * Mapbox GL JS + Recorrido 3D (Earth Studio / iframes).
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Token de Mapbox. Prioridad: constante MARYMED_MAPBOX_TOKEN > Customizer
 * > filtro 'marymed_mapbox_token' (hook para inyectarlo sin tocar codigo).
 */
function marymed_mapbox_token() {
	$token = defined( 'MARYMED_MAPBOX_TOKEN' ) ? MARYMED_MAPBOX_TOKEN : get_theme_mod( 'marymed_mapbox_token', '' );
	return apply_filters( 'marymed_mapbox_token', (string) $token );
}

/**
 * Mapa interactivo Mapbox GL usando las coordenadas del grupo ACF
 * `ubicacion_mapbox` (lat, lng, zoom). Emite todo el HTML/JS autocontenido.
 *
 * @param int  $post_id ID del post.
 * @param bool $echo    True imprime, false devuelve.
 * @return string HTML del mapa o '' si faltan datos/token.
 */
function marymed_mapbox_map( $post_id = 0, $echo = true ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! function_exists( 'get_field' ) || ! $post_id ) {
		return '';
	}

	$location = get_field( 'ubicacion_mapbox', $post_id );
	if ( empty( $location['lat'] ) || empty( $location['lng'] ) ) {
		return '';
	}

	$token = marymed_mapbox_token();
	if ( ! $token ) {
		// Aviso solo para administradores en front.
		if ( current_user_can( 'manage_options' ) ) {
			$out = '<p style="color:#b00020">' . esc_html__( 'Marymed: falta configurar el Mapbox Access Token (Apariencia > Personalizar > Marymed).', 'marymed' ) . '</p>';
			if ( $echo ) {
				echo wp_kses_post( $out );
			}
			return $out;
		}
		return '';
	}

	$lat  = (float) $location['lat'];
	$lng  = (float) $location['lng'];
	$zoom = ! empty( $location['zoom'] ) ? (float) $location['zoom'] : 15;
	$id   = 'marymed-map-' . absint( $post_id );

	$out  = '<link href="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.css" rel="stylesheet">';
	$out .= '<div id="' . esc_attr( $id ) . '" class="marymed-mapbox-canvas"></div>';
	$out .= '<script src="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.js"></script>';
	$out .= '<script>(function(){' .
		'var t=' . wp_json_encode( $token ) . ',' .
		'c=' . wp_json_encode( array( $lng, $lat ) ) . ',' .
		'z=' . (float) $zoom . ';' .
		'mapboxgl.accessToken=t;' .
		'var map=new mapboxgl.Map({container:' . wp_json_encode( $id ) . ',style:"mapbox://styles/mapbox/streets-v12",center:c,zoom:z});' .
		'new mapboxgl.Marker({color:"#e11d48"}).setLngLat(c).addTo(map);' .
		'map.addControl(new mapboxgl.NavigationControl(),"top-right");' .
		'window.addEventListener("load",function(){setTimeout(function(){map.resize();},300);});' .
		'})();</script>';

	if ( $echo ) {
		echo $out; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML y script Mapbox.
		return '';
	}
	return $out;
}
add_shortcode( 'marymed_mapa', 'marymed_mapbox_map' );

/**
 * Inyecta el codigo de recorrido 3D (Earth Studio/Mapbox iframe) que el
 * editor pego en el campo `codigo_3d_recorrido`. Devuelve '' si vacio
 * para permitir la ocultacion condicional en Bricks.
 *
 * @param int $post_id ID del post.
 * @return string Contenido embebido o ''.
 */
function marymed_recorrido_3d( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( function_exists( 'get_field' ) ) {
		$html = get_field( 'codigo_3d_recorrido', $post_id );
	} else {
		$html = '';
	}

	if ( empty( $html ) ) {
		return '';
	}
	return '<div class="marymed-3d-recorrido">' . $html . '</div>';
}
add_shortcode( 'marymed_recorrido_3d', 'marymed_recorrido_3d' );
