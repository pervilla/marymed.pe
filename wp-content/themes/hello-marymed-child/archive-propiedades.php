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
$precio_min   = isset( $_GET['precio_min'] ) ? (int) $_GET['precio_min'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$precio_max   = isset( $_GET['precio_max'] ) ? (int) $_GET['precio_max'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$current_orden = isset( $_GET['orden'] ) ? sanitize_key( wp_unslash( $_GET['orden'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
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

		<label>
			<?php esc_html_e( 'Precio USD desde', 'marymed' ); ?>
			<input type="number" min="0" name="precio_min" value="<?php echo esc_attr( $precio_min ); ?>" placeholder="1000">
		</label>

		<label>
			<?php esc_html_e( 'Precio USD hasta', 'marymed' ); ?>
			<input type="number" min="0" name="precio_max" value="<?php echo esc_attr( $precio_max ); ?>" placeholder="500000">
		</label>

		<label>
			<?php esc_html_e( 'Ordenar por', 'marymed' ); ?>
			<select name="orden">
				<option value="" <?php selected( $current_orden, '' ); ?>><?php esc_html_e( 'Mas recientes', 'marymed' ); ?></option>
				<option value="precio_asc" <?php selected( $current_orden, 'precio_asc' ); ?>><?php esc_html_e( 'Precio: menor a mayor', 'marymed' ); ?></option>
				<option value="precio_desc" <?php selected( $current_orden, 'precio_desc' ); ?>><?php esc_html_e( 'Precio: mayor a menor', 'marymed' ); ?></option>
			</select>
		</label>

		<button class="mm-btn" type="submit"><?php esc_html_e( 'Filtrar', 'marymed' ); ?></button>
		<?php if ( $current_tipo || $current_op || $current_zona || $precio_min || $precio_max || $current_orden ) : ?>
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
