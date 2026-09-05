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
 * Clave meta del precio segun CPT.
 */
function marymed_price_key( $post_type ) {
	return ( 'propiedades' === $post_type ) ? 'precio_usd' : 'precio_vehiculo';
}

/**
 * Filtro de rango de precio (precio_min / precio_max, numerico).
 *
 * @param string $post_type propiedades|vehiculos
 * @param array  $source    $_GET / $_REQUEST.
 * @return array Clausula(s) para el meta_query (o vacio).
 */
function marymed_price_range_meta( $post_type, $source = null ) {
	$source = null === $source ? $_GET : $source; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$min = isset( $source['precio_min'] ) && '' !== $source['precio_min'] ? (float) $source['precio_min'] : 0;
	$max = isset( $source['precio_max'] ) && '' !== $source['precio_max'] ? (float) $source['precio_max'] : 0;

	if ( ! $min && ! $max ) {
		return array();
	}

	$clause = array(
		'key'  => marymed_price_key( $post_type ),
		'type' => 'NUMERIC',
	);

	if ( $min && $max ) {
		$clause['compare'] = 'BETWEEN';
		$clause['value']   = array( $min, $max );
	} elseif ( $min ) {
		$clause['compare'] = '>=';
		$clause['value']   = $min;
	} else {
		$clause['compare'] = '<=';
		$clause['value']   = $max;
	}

	return $clause;
}

/**
 * Args de ordenamiento (WP_Query) segun ?orden=.
 *
 * @param string $post_type propiedades|vehiculos
 * @param array  $source    $_GET / $_REQUEST.
 * @return array Args orderby/order/meta_key o vacio.
 */
function marymed_archive_sort_args( $post_type, $source = null ) {
	$source = null === $source ? $_GET : $source; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$orden  = isset( $source['orden'] ) ? sanitize_key( $source['orden'] ) : '';

	if ( 'precio_asc' === $orden || 'precio_desc' === $orden ) {
		return array(
			'orderby'  => 'meta_value_num',
			'meta_key' => marymed_price_key( $post_type ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'order'    => ( 'precio_desc' === $orden ) ? 'DESC' : 'ASC',
		);
	}

	return array();
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

	// Rango de precio (ambos CPT).
	$price = marymed_price_range_meta( $post_type, $source );
	if ( $price ) {
		$relation[] = $price;
	}

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
	$sort      = marymed_archive_sort_args( $post_type );

	if ( $meta ) {
		$query->set( 'meta_query', $meta );
	}
	if ( $sort ) {
		foreach ( $sort as $k => $v ) {
			$query->set( $k, $v );
		}
	}
}
add_action( 'pre_get_posts', 'marymed_archive_filters' );
