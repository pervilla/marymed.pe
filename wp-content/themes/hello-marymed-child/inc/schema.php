<?php
/**
 * Esquemas JSON-LD para indexacion GEO/SEO (autonomo, sin plugins).
 *
 * Genera RealEstateListing (propiedades) y Vehicle (vehiculos) con los
 * campos ACF. Se auto-emite en <head> cuando NO hay Rank Math; si Rank
 * Math esta activo, inyecta el nodo en su grafo para evitar duplicados.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nodo de esquema de una propiedad (RealEstateListing).
 */
function marymed_property_schema( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$excerpt = get_the_excerpt( $post_id );
	$desc    = $excerpt ? $excerpt : wp_trim_words( get_the_content( $post_id ), 40 );

	$schema = array(
		'@type'            => 'RealEstateListing',
		'name'             => get_the_title( $post_id ),
		'url'              => get_permalink( $post_id ),
		'description'      => wp_strip_all_tags( $desc ),
		'datePosted'       => get_the_date( 'c', $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'image'            => get_the_post_thumbnail_url( $post_id, 'full' ),
	);

	// --- Oferta en USD (+ PEN como precio alterno). ---
	$price_usd = (float) get_field( 'precio_usd', $post_id );
	$price_pen = (float) get_field( 'precio_pen', $post_id );

	if ( $price_usd > 0 || $price_pen > 0 ) {
		$offer = array(
			'@type'         => 'Offer',
			'url'           => get_permalink( $post_id ),
			'availability'  => 'https://schema.org/InStock',
			'priceCurrency' => 'USD',
			'price'         => $price_usd > 0 ? $price_usd : $price_pen,
		);

		$area = (float) get_field( 'area_total', $post_id );
		if ( $area > 0 ) {
			$offer['floorSize'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => $area,
				'unitCode' => 'MTK',
			);
		}
		$schema['offers'] = $offer;
	}

	$tipo = get_field( 'tipo_inmueble', $post_id );
	$op   = get_field( 'tipo_operacion', $post_id );
	if ( $tipo ) {
		$schema['category'] = $tipo;
	}
	if ( $op ) {
		$schema['businessFunction'] = ( 'Alquiler' === $op )
			? 'https://schema.org/LeaseOut'
			: 'https://schema.org/Sell';
	}

	// --- Ubicacion: PostalAddress + GeoCoordinates. ---
	$location = marymed_location_data( $post_id );
	if ( ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
		$schema['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $location['lat'],
			'longitude' => (float) $location['lng'],
		);
	}

	$address = array( '@type' => 'PostalAddress' );
	if ( ! empty( $location['zona'] ) ) {
		$address['addressLocality'] = $location['zona'];
	}
	if ( ! empty( $location['direccion'] ) ) {
		$address['streetAddress'] = $location['direccion'];
	}
	if ( count( $address ) > 1 ) {
		$schema['address'] = $address;
	}

	return array_filter( $schema );
}

/**
 * Nodo de esquema de un vehiculo (Vehicle).
 */
function marymed_vehicle_schema( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$excerpt = get_the_excerpt( $post_id );
	$desc    = $excerpt ? $excerpt : wp_trim_words( get_the_content( $post_id ), 40 );

	$schema = array(
		'@type'       => 'Vehicle',
		'name'        => get_the_title( $post_id ),
		'url'         => get_permalink( $post_id ),
		'description' => wp_strip_all_tags( $desc ),
		'image'       => get_the_post_thumbnail_url( $post_id, 'full' ),
	);

	$tipo = get_field( 'tipo_vehiculo', $post_id );
	if ( $tipo ) {
		$schema['vehicleType'] = $tipo;
	}

	$anio = (int) get_field( 'anio_vehiculo', $post_id );
	if ( $anio > 0 ) {
		$schema['vehicleModelDate'] = (string) $anio;
	}

	$km = (float) get_field( 'kilometraje_vehiculo', $post_id );
	if ( $km > 0 ) {
		$schema['mileageFromOdometer'] = array(
			'@type'    => 'QuantitativeValue',
			'value'    => $km,
			'unitCode' => 'KMT',
		);
	}

	$transmision = get_field( 'transmision_vehiculo', $post_id );
	if ( $transmision ) {
		$schema['vehicleTransmission'] = $transmision;
	}

	$price = (float) get_field( 'precio_vehiculo', $post_id );
	if ( $price > 0 ) {
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $price,
			'priceCurrency' => 'USD',
			'availability'  => 'https://schema.org/InStock',
			'url'           => get_permalink( $post_id ),
		);
	}

	return array_filter( $schema );
}

/**
 * Auto-emision en <head> cuando NO esta activo Rank Math.
 */
function marymed_output_jsonld() {
	// Si Rank Math esta activo, el nodo se inyecta via su filtro (abajo).
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	if ( ! is_singular( array( 'propiedades', 'vehiculos' ) ) ) {
		return;
	}

	$post_id = get_the_ID();
	$type    = get_post_type( $post_id );

	$schema = ( 'vehiculos' === $type )
		? marymed_vehicle_schema( $post_id )
		: marymed_property_schema( $post_id );

	if ( empty( $schema ) ) {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', $schema ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'wp_head', 'marymed_output_jsonld', 15 );

/**
 * Si Rank Math esta presente, se integra a su grafo (sin duplicar).
 */
function marymed_rank_math_schema( $data, $jsonld = array() ) {
	if ( ! is_singular( array( 'propiedades', 'vehiculos' ) ) ) {
		return $data;
	}

	$post_id = get_the_ID();

	if ( 'propiedades' === get_post_type( $post_id ) ) {
		$data['marymed_property'] = marymed_property_schema( $post_id );
	} else {
		$data['marymed_vehicle'] = marymed_vehicle_schema( $post_id );
	}

	return $data;
}

if ( defined( 'RANK_MATH_VERSION' ) ) {
	add_filter( 'rank_math/json_ld', 'marymed_rank_math_schema', 20, 2 );
}
