<?php
/**
 * Custom Post Types: propiedades y vehiculos.
 *
 * Los permalinks deben quedar como  /%postname%/  =>  /propiedades/nombre/
 * Tras activar el tema se regeneran las reglas de reescritura.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registro de ambos CPT en init con prioridad 0 para que esten listos
 * antes que cualquier otro proceso (taxonomias, ACF, Rank Math...).
 */
function marymed_register_post_types() {

	// ------------------------------------------------------------
	// CPT: Propiedades  (slug: propiedades)
	// ------------------------------------------------------------
	register_post_type(
		'propiedades',
		array(
			'label'        => __( 'Propiedades', 'marymed' ),
			'labels'       => array(
				'name'               => __( 'Propiedades', 'marymed' ),
				'singular_name'      => __( 'Propiedad', 'marymed' ),
				'menu_name'          => __( 'Propiedades', 'marymed' ),
				'add_new'            => __( 'Anadir Propiedad', 'marymed' ),
				'add_new_item'       => __( 'Anadir nueva Propiedad', 'marymed' ),
				'edit_item'          => __( 'Editar Propiedad', 'marymed' ),
				'new_item'           => __( 'Nueva Propiedad', 'marymed' ),
				'view_item'          => __( 'Ver Propiedad', 'marymed' ),
				'search_items'       => __( 'Buscar Propiedades', 'marymed' ),
				'not_found'          => __( 'No se encontraron Propiedades', 'marymed' ),
				'not_found_in_trash' => __( 'No hay Propiedades en papelera', 'marymed' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-building',
			'menu_position'=> 5,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'rewrite'      => array(
				'slug'       => 'propiedades',
				'with_front' => false,
			),
			'capability_type' => 'post',
		)
	);

	// ------------------------------------------------------------
	// CPT: Vehiculos  (slug: vehiculos)
	// ------------------------------------------------------------
	register_post_type(
		'vehiculos',
		array(
			'label'        => __( 'Vehiculos', 'marymed' ),
			'labels'       => array(
				'name'               => __( 'Vehiculos', 'marymed' ),
				'singular_name'      => __( 'Vehiculo', 'marymed' ),
				'menu_name'          => __( 'Vehiculos', 'marymed' ),
				'add_new'            => __( 'Anadir Vehiculo', 'marymed' ),
				'add_new_item'       => __( 'Anadir nuevo Vehiculo', 'marymed' ),
				'edit_item'          => __( 'Editar Vehiculo', 'marymed' ),
				'new_item'           => __( 'Nuevo Vehiculo', 'marymed' ),
				'view_item'          => __( 'Ver Vehiculo', 'marymed' ),
				'search_items'       => __( 'Buscar Vehiculos', 'marymed' ),
				'not_found'          => __( 'No se encontraron Vehiculos', 'marymed' ),
				'not_found_in_trash' => __( 'No hay Vehiculos en papelera', 'marymed' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-car',
			'menu_position'=> 6,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'rewrite'      => array(
				'slug'       => 'vehiculos',
				'with_front' => false,
			),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'marymed_register_post_types', 0 );

/**
 * Regenera los permalinks al activar el tema hijo.
 */
function marymed_flush_rewrite_rules() {
	marymed_register_post_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'marymed_flush_rewrite_rules' );
