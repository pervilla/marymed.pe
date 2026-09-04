<?php
/**
 * Archivo del CPT `propiedades` con filtros GET (base del catalogo).
 *
 * @package Marymed
 */

get_header();

$choices      = marymed_filter_choices();
$current_tipo = marymed_choice_value( 'tipo_inmueble', $choices['tipo_inmueble'] );
$current_op   = marymed_choice_value( 'tipo_operacion', $choices['tipo_operacion'] );
$current_zona = isset( $_GET['zona'] ) ? sanitize_text_field( wp_unslash( $_GET['zona'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<div class="mm-container">

	<header class="mm-archive-head">
		<h1><?php esc_html_e( 'Propiedades en Venta y Alquiler', 'marymed' ); ?></h1>
	</header>

	<form class="mm-filters" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'propiedades' ) ); ?>">
		<label>
			<?php esc_html_e( 'Tipo de inmueble', 'marymed' ); ?>
			<select name="tipo_inmueble">
				<option value=""><?php esc_html_e( 'Todos', 'marymed' ); ?></option>
				<?php foreach ( array( 'Lote', 'Casa', 'Departamento', 'Edificio' ) as $t ) : ?>
					<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $current_tipo, $t ); ?>><?php echo esc_html( $t ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>
			<?php esc_html_e( 'Operacion', 'marymed' ); ?>
			<select name="tipo_operacion">
				<option value=""><?php esc_html_e( 'Todas', 'marymed' ); ?></option>
				<?php foreach ( array( 'Venta', 'Alquiler' ) as $o ) : ?>
					<option value="<?php echo esc_attr( $o ); ?>" <?php selected( $current_op, $o ); ?>><?php echo esc_html( $o ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>
			<?php esc_html_e( 'Ciudad / Zona', 'marymed' ); ?>
			<input type="text" name="zona" value="<?php echo esc_attr( $current_zona ); ?>" placeholder="<?php esc_attr_e( 'Miraflores, Lima...', 'marymed' ); ?>">
		</label>

		<button class="mm-btn" type="submit"><?php esc_html_e( 'Filtrar', 'marymed' ); ?></button>
		<?php if ( $current_tipo || $current_op || $current_zona ) : ?>
			<a class="mm-btn mm-btn--wa" href="<?php echo esc_url( get_post_type_archive_link( 'propiedades' ) ); ?>"><?php esc_html_e( 'Limpiar', 'marymed' ); ?></a>
		<?php endif; ?>
	</form>

	<div class="mm-results" data-mm-results>
		<?php if ( have_posts() ) : ?>
			<div class="mm-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card', 'propiedad' );
				endwhile;
				?>
			</div>

			<nav class="mm-pagination">
				<?php
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
					)
				);
				?>
			</nav>

		<?php else : ?>
			<p><?php esc_html_e( 'No se encontraron propiedades con esos criterios.', 'marymed' ); ?></p>
		<?php endif; ?>
	</div>

</div>

<?php
get_footer();
