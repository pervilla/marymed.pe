<?php
/**
 * Endpoint AJAX para los listados (propiedades/vehiculos).
 *
 * Devuelve el HTML de la cuadricula + paginacion, aplicando los mismos
 * filtros que el render por GET. Es de solo lectura (sin nonce): la
 * seguridad queda en la validacion de whitelists en archive-filters.php.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Paginacion reutilizando los filtros actuales en la URL base.
 */
function marymed_ajax_pagination( $total, $current ) {
	$cpt        = isset( $_REQUEST['post_type'] ) ? sanitize_key( wp_unslash( $_REQUEST['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$archive_url = get_post_type_archive_link( $cpt );
	if ( ! $archive_url ) {
		$archive_url = home_url( '/' );
	}

	// Refleja solo los parametros de filtro conocidos.
	$allowed = array( 'tipo_inmueble', 'tipo_operacion', 'zona', 'tipo_vehiculo', 'transmision_vehiculo', 'precio_min', 'precio_max', 'orden' );
	$base_q  = '';
	foreach ( $allowed as $key ) {
		if ( isset( $_REQUEST[ $key ] ) && '' !== $_REQUEST[ $key ] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$base_q .= ( '' === $base_q ? '?' : '&' )
				. rawurlencode( $key ) . '=' . rawurlencode( (string) wp_unslash( $_REQUEST[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}
	$base = $archive_url . $base_q . ( '' !== $base_q ? '&' : '?' ) . 'paged=%#%';

	return paginate_links(
		array(
			'base'      => $base,
			'format'    => '',
		'current'   => $current,
		'total'     => $total,
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		)
	);
}

/**
 * Render del area de resultados (grid + paginacion o mensaje vacio).
 */
function marymed_render_archive_results( $cpt, $paged ) {
	$meta    = marymed_archive_meta_query( $cpt, $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sort    = marymed_archive_sort_args( $cpt, $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$perpage = (int) get_option( 'posts_per_page', 10 );

	$query_args = array(
		'post_type'      => $cpt,
		'post_status'    => 'publish',
		'posts_per_page' => $perpage,
		'paged'          => $paged,
		'meta_query'     => $meta,
	);

	$query = new WP_Query( array_merge( $query_args, $sort ) );

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No se encontraron resultados con esos criterios.', 'marymed' ) . '</p>';
	}

	$html = '<div class="mm-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		ob_start();
		get_template_part( 'template-parts/card', ( 'vehiculos' === $cpt ? 'vehiculo' : 'propiedad' ) );
		$html .= ob_get_clean();
	}
	$html .= '</div>';
	wp_reset_postdata();

	if ( $query->max_num_pages > 1 ) {
		$html .= '<nav class="mm-pagination">' . marymed_ajax_pagination( $query->max_num_pages, $paged ) . '</nav>';
	}

	return $html;
}

/**
 * Handler AJAX: marymed_ajax_archive.
 */
function marymed_handle_ajax_archive() {
	$cpt = isset( $_REQUEST['post_type'] ) ? sanitize_key( wp_unslash( $_REQUEST['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! in_array( $cpt, array( 'propiedades', 'vehiculos' ), true ) ) {
		wp_send_json_error( array( 'message' => 'CPT invalido' ) );
	}

	$paged = isset( $_REQUEST['paged'] ) ? max( 1, (int) $_REQUEST['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	wp_send_json_success( array( 'html' => marymed_render_archive_results( $cpt, $paged ) ) );
}
add_action( 'wp_ajax_marymed_ajax_archive', 'marymed_handle_ajax_archive' );
add_action( 'wp_ajax_nopriv_marymed_ajax_archive', 'marymed_handle_ajax_archive' );
