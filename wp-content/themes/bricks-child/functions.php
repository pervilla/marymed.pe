<?php
/**
 * Marymed Real Estate - Bricks Child
 *
 * Punto de entrada del tema hijo. Solo carga utilidades PHP; el diseno
 * visual se administra desde Bricks Builder (Theme Builder), por lo que
 * este tema NO incluye plantillas PHP para no pisar las de Bricks.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

define( 'MARYMED_THEME_VERSION', '1.0.0' );
define( 'MARYMED_THEME_DIR', get_stylesheet_directory() );
define( 'MARYMED_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Carga de modulos internos del tema hijo.
 * Cada archivo de inc/ registra sus propios hooks.
 */
function marymed_load_includes() {
	$modules = array(
		'cpt',           // Registro de CPT: propiedades + vehiculos.
		'acf-fields',    // Grupos de campos ACF (estructura de datos).
		'helpers',       // WhatsApp, TikTok, botones de compartir.
		'mapbox',        // Mapa Mapbox GL + recorrido 3D.
		'schema',        // JSON-LD GEO/SEO para Rank Math.
		'customizer',    // Ajustes: numero WhatsApp, Mapbox token, TikTok.
	);

	foreach ( $modules as $module ) {
		$file = MARYMED_THEME_DIR . '/inc/' . $module . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
add_action( 'after_setup_theme', 'marymed_load_includes' );

/**
 * Registra estilos del tema padre (bricks) y del hijo.
 * Bricks expone 'bricks-frontend' cuando esta activo.
 */
function marymed_enqueue_styles() {
	$parent_version = ( wp_get_theme()->parent() ) ? wp_get_theme()->parent()->get( 'Version' ) : '';

	if ( ! wp_style_is( 'bricks-frontend', 'registered' ) ) {
		// Fallback por si Bricks no esta activo (no debe pasar en produccion).
		wp_enqueue_style( 'marymed-bricks-style', get_template_directory_uri() . '/style.css', array(), $parent_version );
	}

	wp_enqueue_style(
		'marymed-child-style',
		MARYMED_THEME_URI . '/style.css',
		array( 'bricks-frontend' ),
		MARYMED_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'marymed_enqueue_styles', 20 );

/**
 * Soporte basico del tema.
 */
function marymed_theme_support() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	load_child_theme_textdomain( 'marymed', MARYMED_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'marymed_theme_support', 0 );

/**
 * Clases utiles en <body> segun el CPT.
 */
function marymed_body_classes( $classes ) {
	if ( is_singular( array( 'propiedades', 'vehiculos' ) ) ) {
		$classes[] = 'marymed-single marymed-' . get_post_type();
	}
	return $classes;
}
add_filter( 'body_class', 'marymed_body_classes' );

/**
 * Desactiva la admin bar en el frontend para no romper el diseno de Bricks.
 * Comenta la linea si quieres mantenerla para usuarios logueados.
 */
// add_filter( 'show_admin_bar', '__return_false' );
