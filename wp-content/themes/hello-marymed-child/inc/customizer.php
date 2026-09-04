<?php
/**
 * Ajustes del tema via Customizer (Apariencia > Personalizar > Marymed):
 *  - Numero de WhatsApp (con pais).
 *  - Usuario corporativo de TikTok (para Smash Balloon TikTok Feeds).
 *
 * El mapa usa Leaflet/OSM (gratis): no requiere token.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

function marymed_sanitize_digits( $value ) {
	return preg_replace( '/[^0-9]/', '', (string) $value );
}

function marymed_sanitize_text( $value ) {
	return sanitize_text_field( $value );
}

function marymed_sanitize_textarea( $value ) {
	return sanitize_textarea_field( $value );
}

/**
 * Registro de la seccion y controles.
 */
function marymed_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'marymed_integrations',
		array(
			'title'    => __( 'Marymed', 'marymed' ),
			'priority' => 130,
		)
	);

	// --- WhatsApp ---
	$wp_customize->add_setting(
		'marymed_whatsapp',
		array(
			'default'           => '',
			'sanitize_callback' => 'marymed_sanitize_digits',
		)
	);
	$wp_customize->add_control(
		'marymed_whatsapp',
		array(
			'label'       => __( 'Numero de WhatsApp', 'marymed' ),
			'description' => __( 'Con codigo de pais. Ej: 51999888777', 'marymed' ),
			'section'     => 'marymed_integrations',
			'type'        => 'text',
		)
	);

	// --- TikTok (Smash Balloon TikTok Feeds) ---
	$wp_customize->add_setting(
		'marymed_tiktok_user',
		array(
			'default'           => '',
			'sanitize_callback' => 'marymed_sanitize_text',
		)
	);
	$wp_customize->add_control(
		'marymed_tiktok_user',
		array(
			'label'       => __( 'Usuario TikTok corporativo', 'marymed' ),
			'description' => __( 'Sin @. Ej: marymed', 'marymed' ),
			'section'     => 'marymed_integrations',
			'type'        => 'text',
		)
	);

	// --- Videos TikTok para la Home (feed sin plugins) ---
	$wp_customize->add_setting(
		'marymed_tiktok_videos',
		array(
			'default'           => '',
			'sanitize_callback' => 'marymed_sanitize_textarea',
		)
	);
	$wp_customize->add_control(
		'marymed_tiktok_videos',
		array(
			'label'       => __( 'Videos TikTok de la Home', 'marymed' ),
			'description' => __( 'Un enlace de video por linea (max. 4). Se muestran en la portada.', 'marymed' ),
			'section'     => 'marymed_integrations',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'marymed_customize_register' );

/**
 * Devuelve el handle de TikTok guardado (para feeds de Smash Balloon).
 */
function marymed_tiktok_handle() {
	return apply_filters( 'marymed_tiktok_handle', get_theme_mod( 'marymed_tiktok_user', '' ) );
}
