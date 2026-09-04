<?php
/**
 * Ajustes del tema via Customizer (Apariencia > Personalizar > Marymed):
 *  - Numero de WhatsApp (con pais).
 *  - Mapbox Access Token.
 *  - Usuario corporativo de TikTok (para el feed de Smash Balloon).
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

	// --- Mapbox ---
	$wp_customize->add_setting(
		'marymed_mapbox_token',
		array(
			'default'           => '',
			'sanitize_callback' => 'marymed_sanitize_text',
		)
	);
	$wp_customize->add_control(
		'marymed_mapbox_token',
		array(
			'label'       => __( 'Mapbox Access Token', 'marymed' ),
			'description' => __( 'pk.xxx de https://account.mapbox.com', 'marymed' ),
			'section'     => 'marymed_integrations',
			'type'        => 'password',
		)
	);

	// --- TikTok (Smash Balloon) ---
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
}
add_action( 'customize_register', 'marymed_customize_register' );

/**
 * Valor util para pasar al widget de Smash Balloon TikTok Feeds:
 * devuelve el arreglo listo para shortcode, p.ej. para inyectarlo via
 * do_shortcode() desde una plantilla o el builder.
 *
 * Uso: echo do_shortcode( sprintf( '[fts_tiktok ... ]', marymed_tiktok_shortcode_args() ) );
 */
function marymed_tiktok_shortcode_args() {
	$user = get_theme_mod( 'marymed_tiktok_user', '' );
	return apply_filters( 'marymed_tiktok_handle', $user );
}
