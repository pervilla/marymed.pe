<?php
/**
 * Marymed Real Estate - Hello Elementor Child
 *
 * Tema hijo de Hello Elementor con plantillas PHP nativas para los CPT
 * "propiedades" y "vehiculos". Las paginas estaticas (Inicio, Contacto)
 * pueden maquetarse con Elementor (gratis); las fichas dinamicas viven
 * en este tema (single-propiedades.php / single-vehiculos.php).
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

define( 'MARYMED_THEME_VERSION', '1.1.0' );
define( 'MARYMED_THEME_DIR', get_stylesheet_directory() );
define( 'MARYMED_THEME_URI', get_stylesheet_directory_uri() );

/**
 * Carga de modulos internos del tema hijo.
 */
function marymed_load_includes() {
	$modules = array(
		'cpt',           // Registro de CPT: propiedades + vehiculos.
		'acf-fields',    // Grupos de campos ACF (estructura de datos).
		'helpers',       // WhatsApp, TikTok, botones de compartir.
		'leaflet',       // Mapa gratuito Leaflet (lat/lng de ACF).
		'schema',        // JSON-LD GEO/SEO autonomo.
		'customizer',    // Ajustes: numero WhatsApp + TikTok.
		'archive-filters', // Filtros GET en listados (propiedades/vehiculos).
		'ajax',          // Endpoint AJAX para los listados.
		'gallery',       // Galeria de imagenes con lightbox.
		'seo',           // Meta description + Open Graph.
		'performance',   // Limpieza de emojis/oEmbed y preconnect.
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
 * Registra el estilo del tema padre (Hello Elementor) y del hijo.
 */
function marymed_enqueue_styles() {
	wp_enqueue_style( 'hello-elementor', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'marymed-child-style',
		MARYMED_THEME_URI . '/style.css',
		array( 'hello-elementor' ),
		MARYMED_THEME_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'marymed_enqueue_styles', 20 );

/**
 * Tipografias de Google Fonts (Inter + Sora).
 */
function marymed_enqueue_fonts() {
	wp_enqueue_style(
		'marymed-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'marymed_enqueue_fonts', 19 );

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
 * JS de filtrado AJAX en los archivos de propiedades/vehiculos.
 * Progressive enhancement: si JS falla, el formulario hace GET normal.
 */
function marymed_enqueue_archive_js() {
	if ( ! is_post_type_archive( array( 'propiedades', 'vehiculos' ) ) ) {
		return;
	}

	$queried = get_queried_object();
	$cpt     = ( $queried && isset( $queried->name ) ) ? $queried->name : get_query_var( 'post_type' );

	wp_enqueue_script(
		'marymed-archive-js',
		MARYMED_THEME_URI . '/assets/js/marymed-archive.js',
		array(),
		MARYMED_THEME_VERSION,
		true
	);

	wp_localize_script(
		'marymed-archive-js',
		'MARYMED_AJAX',
		array(
			'url' => admin_url( 'admin-ajax.php' ),
			'cpt' => is_array( $cpt ) ? $cpt[0] : $cpt,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'marymed_enqueue_archive_js', 30 );

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
 * Acorta el titulo del post para los <title> de SEO (util, no obligatorio).
 */
function marymed_document_title_parts( $title ) {
	return $title;
}
add_filter( 'document_title_parts', 'marymed_document_title_parts' );
