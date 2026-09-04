<?php
/**
 * Esquemas JSON-LD para indexacion GEO/SEO (Rank Math Pro).
 *
 * Inyecta nodos semanticos nuevos en el grafo de Rank Math:
 *  - CPT `propiedades` -> RealEstateListing + GeoCoordinates.
 *  - CPT `vehiculos`   -> Vehicle + Offer.
 *
 * Requiere que los campos ACF coincidan con los slugs de acf-fields.php.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Construye el nodo de esquema de una propiedad.
 */
function marymed_property_schema( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$schema = array(
		'@type'        => 'RealEstateListing',
		'name'         => get_the_title( $post_id ),
		'url'          => get_permalink( $post_id ),
		'description'  => wp_strip_all_tags( get_the_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_the_content( $post_id ) ),
		'datePosted'   => get_the_date( 'c', $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
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

		// Area total -> floorSize (m2).
		$area = (float) get_field( 'area_total', $post_id );
		if ( $area > 0 ) {
			$offer['floorSize'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => $area,
				'unitCode' => 'MTK',
			);
		}

		if ( $price_pen > 0 && $price_usd > 0 ) {
			$schema['priceSpecification'] = array(
				'@type'          => 'PriceSpecification',
				'price'          => $price_pen,
				'priceCurrency'  => 'PEN',
			);
		}

		$schema['offers'] = $offer;
	}

	// --- Tipo de inmueble y operacion (categorias utiles para IA). ---
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
	$location = get_field( 'ubicacion_mapbox', $post_id );
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

	return $schema;
}

/**
 * Construye el nodo de esquema de un vehiculo.
 */
function marymed_vehicle_schema( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$schema = array(
		'@type'       => 'Vehicle',
		'name'        => get_the_title( $post_id ),
		'url'         => get_permalink( $post_id ),
		'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_the_content( $post_id ) ),
		'image'       => get_the_post_thumbnail_url( $post_id, 'full' ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : '',
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

	// --- Oferta (Product/Offer dentro del Vehicle). ---
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

	return $schema;
}

/**
 * Filtra el JSON-LD generado por Rank Math en paginas singulares.
 */
function marymed_rank_math_schema( $data, $jsonld = array() ) {
	if ( ! is_singular( array( 'propiedades', 'vehiculos' ) ) ) {
		return $data;
	}

	$post_id   = get_the_ID();
	$post_type = get_post_type( $post_id );

	if ( 'propiedades' === $post_type ) {
		$schema = marymed_property_schema( $post_id );
		if ( ! empty( $schema ) ) {
			$data['marymed_property'] = $schema;
		}
	} elseif ( 'vehiculos' === $post_type ) {
		$schema = marymed_vehicle_schema( $post_id );
		if ( ! empty( $schema ) ) {
			$data['marymed_vehicle'] = $schema;
		}
	}

	return $data;
}

if ( defined( 'RANK_MATH_VERSION' ) ) {
	add_filter( 'rank_math/json_ld', 'marymed_rank_math_schema', 20, 2 );
}
