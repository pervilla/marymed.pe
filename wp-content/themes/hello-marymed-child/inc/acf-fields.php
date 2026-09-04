<?php
/**
 * Grupos de campos ACF - Estructura de datos de Marymed.
 *
 * Se registran via acf_add_local_field_group() para que la estructura
 * viva en codigo (versionable) y se cree automaticamente al activar el
 * tema. Slugs documentados para uso en Bricks, FacetWP y JSON-LD:
 *
 *  CPT propiedades -> group_marymed_propiedades
 *  CPT vehiculos   -> group_marymed_vehiculos
 *
 * NOTA DE FILTRADO (FacetWP): FacetWP puede indexar estos campos ACF como
 * data source. Para mejor rendimiento y facetas "checkbox/select" nativas,
 * se recomienda migrar `tipo_inmueble`, `tipo_operacion` y `zona` a
 * taxonomias reales; el codigo siguiente mantiene la fidelidad al spec
 * (campos ACF) y deja el mapeo de facetas documentado.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registro de grupos solo si ACF esta activo.
 */
function marymed_register_acf_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// ============================================================
	// ENTIDAD 1: Propiedades
	// ============================================================
	acf_add_local_field_group(
		array(
			'key'      => 'group_marymed_propiedades',
			'title'    => __( 'Ficha Propiedad', 'marymed' ),
			'fields'   => array(

				// Tipo de inmueble -> Facet select.
				array(
					'key'           => 'field_marymed_prop_tipo_inmueble',
					'label'         => __( 'Tipo de Inmueble', 'marymed' ),
					'name'          => 'tipo_inmueble',
					'type'          => 'select',
					'instructions'  => __( 'Lote, Casa, Departamento o Edificio.', 'marymed' ),
					'choices'       => array(
						'Lote'         => __( 'Lote', 'marymed' ),
						'Casa'         => __( 'Casa', 'marymed' ),
						'Departamento' => __( 'Departamento', 'marymed' ),
						'Edificio'     => __( 'Edificio', 'marymed' ),
					),
					'default_value' => 'Casa',
					'return_format' => 'value',
					'allow_null'    => 0,
					'wrapper'       => array( 'width' => '33' ),
				),

				// Operacion -> Facet select.
				array(
					'key'           => 'field_marymed_prop_tipo_operacion',
					'label'         => __( 'Operacion', 'marymed' ),
					'name'          => 'tipo_operacion',
					'type'          => 'select',
					'instructions'  => __( 'Venta o Alquiler.', 'marymed' ),
					'choices'       => array(
						'Venta'    => __( 'Venta', 'marymed' ),
						'Alquiler' => __( 'Alquiler', 'marymed' ),
					),
					'default_value' => 'Venta',
					'return_format' => 'value',
					'allow_null'    => 0,
					'wrapper'       => array( 'width' => '33' ),
				),

				// Precio USD -> Schema Offer.price.
				array(
					'key'          => 'field_marymed_prop_precio_usd',
					'label'        => __( 'Precio en USD', 'marymed' ),
					'name'         => 'precio_usd',
					'type'         => 'number',
					'instructions' => __( 'Usado en el Schema como Offer.priceCurrency = USD.', 'marymed' ),
					'min'          => 0,
					'step'         => 'any',
					'append'       => 'USD',
					'wrapper'      => array( 'width' => '33' ),
				),

				// Precio PEN.
				array(
					'key'     => 'field_marymed_prop_precio_pen',
					'label'   => __( 'Precio en PEN', 'marymed' ),
					'name'    => 'precio_pen',
					'type'    => 'number',
					'min'     => 0,
					'step'    => 'any',
					'append'  => 'PEN',
					'wrapper' => array( 'width' => '33' ),
				),

				// Area total m2 -> Schema Offer.floorSize.
				array(
					'key'     => 'field_marymed_prop_area_total',
					'label'   => __( 'Area Total (m2)', 'marymed' ),
					'name'    => 'area_total',
					'type'    => 'number',
					'min'     => 0,
					'step'    => 'any',
					'append'  => 'm2',
					'wrapper' => array( 'width' => '33' ),
				),

				// Ubicacion geografica -> grupo lat/lng/zoom/zona.
				// Fiel al spec (slug ubicacion_mapbox) pero sin requerir API
				// key en el admin; Mapbox GL renderiza en el frontend.
				array(
					'key'          => 'field_marymed_prop_ubicacion_mapbox',
					'label'        => __( 'Ubicacion Geografica (Mapbox)', 'marymed' ),
					'name'         => 'ubicacion_mapbox',
					'type'         => 'group',
					'instructions' => __( 'Coordenadas Lat/Lng y zona para el mapa Mapbox y el filtro por ubicacion.', 'marymed' ),
					'layout'       => 'block',
					'sub_fields'   => array(
						array(
							'key'          => 'field_marymed_prop_ubicacion_lat',
							'label'        => __( 'Latitud', 'marymed' ),
							'name'         => 'lat',
							'type'         => 'text',
							'instructions' => __( 'Ej: -12.046374', 'marymed' ),
							'wrapper'      => array( 'width' => '25' ),
						),
						array(
							'key'          => 'field_marymed_prop_ubicacion_lng',
							'label'        => __( 'Longitud', 'marymed' ),
							'name'         => 'lng',
							'type'         => 'text',
							'instructions' => __( 'Ej: -77.042793', 'marymed' ),
							'wrapper'      => array( 'width' => '25' ),
						),
						array(
							'key'          => 'field_marymed_prop_ubicacion_zoom',
							'label'        => __( 'Zoom', 'marymed' ),
							'name'         => 'zoom',
							'type'         => 'number',
							'default_value' => 15,
							'min'          => 3,
							'max'          => 20,
							'wrapper'      => array( 'width' => '15' ),
						),
						array(
							'key'          => 'field_marymed_prop_ubicacion_zona',
							'label'        => __( 'Ciudad / Zona', 'marymed' ),
							'name'         => 'zona',
							'type'         => 'text',
							'instructions' => __( 'Ej: Miraflores, Lima. Alimenta la Facet de ubicacion.', 'marymed' ),
							'wrapper'      => array( 'width' => '35' ),
						),
						array(
							'key'     => 'field_marymed_prop_ubicacion_direccion',
							'label'   => __( 'Direccion / Referencia', 'marymed' ),
							'name'    => 'direccion',
							'type'    => 'text',
							'wrapper' => array( 'width' => '50' ),
						),
					),
				),

				// Video TikTok de la propiedad.
				array(
					'key'          => 'field_marymed_prop_url_tiktok',
					'label'        => __( 'Enlace de Video TikTok', 'marymed' ),
					'name'         => 'url_tiktok_propiedad',
					'type'         => 'url',
					'instructions' => __( 'Pega el enlace del video (https://www.tiktok.com/@usuario/video/123456). Si esta vacio, el bloque se oculta.', 'marymed' ),
					'placeholder'  => 'https://www.tiktok.com/@marymed/video/...',
				),

				// Codigo HTML/iframe para recorrido 3D (Earth Studio/Mapbox).
				array(
					'key'          => 'field_marymed_prop_codigo_3d',
					'label'        => __( 'Recorrido 3D Animado', 'marymed' ),
					'name'         => 'codigo_3d_recorrido',
					'type'         => 'textarea',
					'instructions' => __( 'Pega el <iframe> o <script> de Google Earth Studio / Mapbox. Se inyecta tal cual en el contenedor.', 'marymed' ),
					'new_lines'    => '', // No convertir saltos: respeta el iframe.
					'rows'         => 4,
				),

			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'propiedades',
					),
				),
			),
			'position'          => 'acf_after_title',
			'style'             => 'seamless',
			'label_placement'   => 'top',
			'instruction_placement' => 'label',
		)
	);

	// ============================================================
	// ENTIDAD 2: Vehiculos
	// ============================================================
	acf_add_local_field_group(
		array(
			'key'      => 'group_marymed_vehiculos',
			'title'    => __( 'Ficha Vehiculo', 'marymed' ),
			'fields'   => array(

				array(
					'key'           => 'field_marymed_veh_tipo_vehiculo',
					'label'         => __( 'Tipo de Vehiculo', 'marymed' ),
					'name'          => 'tipo_vehiculo',
					'type'          => 'select',
					'choices'       => array(
						'Auto'      => __( 'Auto', 'marymed' ),
						'Camioneta' => __( 'Camioneta', 'marymed' ),
						'Moto'      => __( 'Moto', 'marymed' ),
						'Minivan'   => __( 'Minivan', 'marymed' ),
						'Bus'       => __( 'Bus', 'marymed' ),
						'Otro'      => __( 'Otro', 'marymed' ),
					),
					'default_value' => 'Auto',
					'return_format' => 'value',
					'allow_null'    => 0,
					'wrapper'       => array( 'width' => '33' ),
				),

				array(
					'key'          => 'field_marymed_veh_precio',
					'label'        => __( 'Precio', 'marymed' ),
					'name'         => 'precio_vehiculo',
					'type'         => 'number',
					'instructions' => __( 'En USD. Se inyecta en el Schema como Offer.price.', 'marymed' ),
					'min'          => 0,
					'step'         => 'any',
					'append'       => 'USD',
					'wrapper'      => array( 'width' => '33' ),
				),

				array(
					'key'          => 'field_marymed_veh_anio',
					'label'        => __( 'Anio de Fabricacion', 'marymed' ),
					'name'         => 'anio_vehiculo',
					'type'         => 'number',
					'min'          => 1950,
					'max'          => 2100,
					'default_value' => (int) gmdate( 'Y' ),
					'wrapper'      => array( 'width' => '33' ),
				),

				array(
					'key'          => 'field_marymed_veh_kilometraje',
					'label'        => __( 'Kilometraje', 'marymed' ),
					'name'         => 'kilometraje_vehiculo',
					'type'         => 'number',
					'min'          => 0,
					'append'       => 'km',
					'wrapper'      => array( 'width' => '33' ),
				),

				array(
					'key'           => 'field_marymed_veh_transmision',
					'label'         => __( 'Transmision', 'marymed' ),
					'name'          => 'transmision_vehiculo',
					'type'          => 'select',
					'choices'       => array(
						'Mecanica'   => __( 'Mecanica', 'marymed' ),
						'Automatica' => __( 'Automatica', 'marymed' ),
					),
					'default_value' => 'Automatica',
					'return_format' => 'value',
					'allow_null'    => 0,
					'wrapper'       => array( 'width' => '33' ),
				),

				array(
					'key'          => 'field_marymed_veh_url_tiktok',
					'label'        => __( 'Enlace de Video TikTok', 'marymed' ),
					'name'         => 'url_tiktok_vehiculo',
					'type'         => 'url',
					'placeholder'  => 'https://www.tiktok.com/@marymed/video/...',
					'instructions' => __( 'Video TikTok de la ficha. Contenedor oculto si esta vacio.', 'marymed' ),
				),

			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'vehiculos',
					),
				),
			),
			'position'          => 'acf_after_title',
			'style'             => 'seamless',
			'label_placement'   => 'top',
			'instruction_placement' => 'label',
		)
	);
}
add_action( 'acf/init', 'marymed_register_acf_groups' );
