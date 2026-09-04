<?php
/**
 * Tarjeta de listado para una propiedad (usada en archive y filtros).
 *
 * @package Marymed
 */

$pid  = get_the_ID();
$tipo = get_field( 'tipo_inmueble', $pid );
$op   = get_field( 'tipo_operacion', $pid );
$usd  = (float) get_field( 'precio_usd', $pid );
$loc  = marymed_location_data( $pid );
?>

<article <?php post_class( 'mm-card' ); ?>>
	<a class="mm-card__media" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large' ); ?>
		<?php endif; ?>
		<?php if ( $usd ) : ?>
			<span class="mm-card__price">$<?php echo esc_html( number_format( $usd ) ); ?></span>
		<?php endif; ?>
	</a>

	<div class="mm-card__body">
		<h3 class="mm-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="mm-badges">
			<?php if ( $tipo ) : ?><span class="mm-badge"><?php echo esc_html( $tipo ); ?></span><?php endif; ?>
			<?php if ( $op ) : ?><span class="mm-badge mm-badge--op"><?php echo esc_html( $op ); ?></span><?php endif; ?>
		</div>

		<?php if ( ! empty( $loc['zona'] ) ) : ?>
			<div class="mm-card__meta"><?php echo esc_html( $loc['zona'] ); ?></div>
		<?php endif; ?>

		<div class="mm-card__cta">
			<a class="mm-btn mm-btn--block" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Ver detalle', 'marymed' ); ?></a>
		</div>
	</div>
</article>
