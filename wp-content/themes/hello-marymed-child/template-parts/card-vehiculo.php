<?php
/**
 * Tarjeta de listado para un vehiculo (usada en archive y filtros).
 *
 * @package Marymed
 */

$pid    = get_the_ID();
$tipo   = get_field( 'tipo_vehiculo', $pid );
$precio = (float) get_field( 'precio_vehiculo', $pid );
$anio   = get_field( 'anio_vehiculo', $pid );
$km     = get_field( 'kilometraje_vehiculo', $pid );
?>

<article <?php post_class( 'mm-card' ); ?>>
	<a class="mm-card__media" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large' ); ?>
		<?php endif; ?>
		<?php if ( $precio ) : ?>
			<span class="mm-card__price">$<?php echo esc_html( number_format( $precio ) ); ?></span>
		<?php endif; ?>
	</a>

	<div class="mm-card__body">
		<h3 class="mm-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="mm-card__meta">
			<?php
			echo esc_html(
				implode( ' | ', array_filter( array( $anio, $tipo, ( '' !== $km ? number_format( (float) $km ) . ' km' : '' ) ) ) )
			);
			?>
		</div>

		<div class="mm-card__cta">
			<a class="mm-btn mm-btn--block" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Ver detalle', 'marymed' ); ?></a>
		</div>
	</div>
</article>
