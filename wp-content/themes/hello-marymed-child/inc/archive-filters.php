<?php
/**
 * Filtros de listados (archive) via parametros GET/AJAX.
 *
 * La logica vive en marymed_archive_meta_query() para que coincidan
 * exactamente el render normal (GET) y el render AJAX (inc/ajax.php).
 * Los valores se validan contra listas permitidas.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valores permitidos (evitan inyeccion de meta queries).
 */
function marymed_filter_choices() {
	return array(
		'tipo_inmueble'        => array( 'Lote', 'Casa', 'Departamento', 'Edificio' ),
		'tipo_operacion'       => array( 'Venta', 'Alquiler' ),
		'tipo_vehiculo'        => array( 'Auto', 'Camioneta', 'Moto', 'Minivan', 'Bus', 'Otro' ),
		'transmision_vehiculo' => array( 'Mecanica', 'Automatica' ),
	);
}

/**
 * Lee y limpia un valor desde $_GET/$_REQUEST contra la lista permitida.
 */
function marymed_choice_value( $key, $allowed, $source = null ) {
	$source = null === $source ? $_GET : $source; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( empty( $source[ $key ] ) ) {
		return '';
	}
	$value = sanitize_text_field( wp_unslash( $source[ $key ] ) );
	return in_array( $value, $allowed, true ) ? $value : '';
}

/**
 * Construye el meta_query a partir de los filtros activos.
 *
 * @param string $post_type propiedades|vehiculos
 * @param array  $source    $_GET (default) o $_REQUEST en AJAX.
 * @return array Meta query listo para WP_Query (o vacio).
 */
function marymed_archive_meta_query( $post_type, $source = null ) {
	$source   = null === $source ? $_GET : $source; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$allowed  = marymed_filter_choices();
	$relation = array( 'relation' => 'AND' );

	if ( 'propiedades' === $post_type ) {
		foreach ( array( 'tipo_inmueble', 'tipo_operacion' ) as $key ) {
			$value = marymed_choice_value( $key, $allowed[ $key ], $source );
			if ( $value ) {
				$relation[] = array( 'key' => $key, 'value' => $value );
			}
		}
		if ( ! empty( $source['zona'] ) ) {
			$zona = sanitize_text_field( wp_unslash( $source['zona'] ) );
			if ( $zona ) {
				$relation[] = array(
					'key'     => 'ubicacion_mapbox_zona',
					'value'   => $zona,
					'compare' => 'LIKE',
				);
			}
		}
	} elseif ( 'vehiculos' === $post_type ) {
		foreach ( array( 'tipo_vehiculo', 'transmision_vehiculo' ) as $key ) {
			$value = marymed_choice_value( $key, $allowed[ $key ], $source );
			if ( $value ) {
				$relation[] = array( 'key' => $key, 'value' => $value );
			}
		}
	}

	return count( $relation ) > 1 ? $relation : array();
}

/**
 * Aplica los filtros a la query principal de archivo (render por GET).
 */
function marymed_archive_filters( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! is_post_type_archive( array( 'propiedades', 'vehiculos' ) ) ) {
		return;
	}

	$post_type = get_query_var( 'post_type' );
	$meta      = marymed_archive_meta_query( $post_type );

	if ( $meta ) {
		$query->set( 'meta_query', $meta );
	}
}
add_action( 'pre_get_posts', 'marymed_archive_filters' );
