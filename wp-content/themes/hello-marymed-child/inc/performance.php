<?php
/**
 * Optimizaciones de rendimiento livianas (frontend).
 *
 * - Quita emojis, oEmbed discovery/host js y generator (menos requests).
 * - Aplica dns-prefetch/preconnect a los CDN externos que usa el tema
 *   (Leaflet/OSM, TikTok embed).
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Desactiva emojis del frontend.
 */
function marymed_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'marymed_disable_emojis' );

/**
 * Quita scripts/discovery de oEmbed globales (el tema no los usa).
 */
function marymed_disable_oembed_head() {
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'embed_oembed_discover', '__return_false' );
}
add_action( 'init', 'marymed_disable_oembed_head' );

/**
 * Oculta el generador de WordPress.
 */
function marymed_remove_wp_generator() {
	remove_action( 'wp_head', 'wp_generator' );
}
add_action( 'init', 'marymed_remove_wp_generator' );

/**
 * Preconnect a los CDN usados por el tema (mapa y TikTok).
 */
function marymed_preconnect_hints() {
	$hosts = array(
		'https://unpkg.com',
		'https://tile.openstreetmap.org',
		'https://www.tiktok.com',
	);

	foreach ( $hosts as $host ) {
		printf( '<link rel="dns-prefetch" href="%s">' . "\n", esc_url( $host ) );
	}
}
add_action( 'wp_head', 'marymed_preconnect_hints', 1 );
