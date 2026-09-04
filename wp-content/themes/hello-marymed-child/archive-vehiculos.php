<?php
/**
 * Archivo del CPT `vehiculos` con filtros GET.
 *
 * @package Marymed
 */

get_header();

$choices = marymed_filter_choices();
$current_tipo  = marymed_choice_value( 'tipo_vehiculo', $choices['tipo_vehiculo'] );
$current_trans = marymed_choice_value( 'transmision_vehiculo', $choices['transmision_vehiculo'] );
?>

<div class="mm-container">

	<header class="mm-archive-head">
		<h1><?php esc_html_e( 'Autos y Motores', 'marymed' ); ?></h1>
	</header>

	<form class="mm-filters" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'vehiculos' ) ); ?>">
		<label>
			<?php esc_html_e( 'Tipo de vehiculo', 'marymed' ); ?>
			<select name="tipo_vehiculo">
				<option value=""><?php esc_html_e( 'Todos', 'marymed' ); ?></option>
				<?php foreach ( $choices['tipo_vehiculo'] as $t ) : ?>
					<option value="<?php echo esc_attr( $t ); ?>" <?php selected( $current_tipo, $t ); ?>><?php echo esc_html( $t ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label>
			<?php esc_html_e( 'Transmision', 'marymed' ); ?>
			<select name="transmision_vehiculo">
				<option value=""><?php esc_html_e( 'Todas', 'marymed' ); ?></option>
				<?php foreach ( $choices['transmision_vehiculo'] as $tr ) : ?>
					<option value="<?php echo esc_attr( $tr ); ?>" <?php selected( $current_trans, $tr ); ?>><?php echo esc_html( $tr ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<button class="mm-btn" type="submit"><?php esc_html_e( 'Filtrar', 'marymed' ); ?></button>
		<?php if ( $current_tipo || $current_trans ) : ?>
			<a class="mm-btn mm-btn--wa" href="<?php echo esc_url( get_post_type_archive_link( 'vehiculos' ) ); ?>"><?php esc_html_e( 'Limpiar', 'marymed' ); ?></a>
		<?php endif; ?>
	</form>

	<div class="mm-results" data-mm-results>
		<?php if ( have_posts() ) : ?>
			<div class="mm-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/card', 'vehiculo' );
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
			<p><?php esc_html_e( 'No se encontraron vehiculos con esos criterios.', 'marymed' ); ?></p>
		<?php endif; ?>
	</div>

</div>

<?php
get_footer();
