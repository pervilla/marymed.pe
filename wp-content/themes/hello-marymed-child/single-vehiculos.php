<?php
/**
 * Plantilla single del CPT `vehiculos`.
 *
 * Ficha tecnica de vehiculo: precio, anio, kilometraje, transmision,
 * video TikTok condicional, WhatsApp flotante y compartir.
 *
 * @package Marymed
 */

get_header();
?>

<div class="mm-container">

	<?php
	while ( have_posts() ) :
		the_post();
		$pid         = get_the_ID();
		$tipo        = get_field( 'tipo_vehiculo', $pid );
		$precio      = (float) get_field( 'precio_vehiculo', $pid );
		$anio        = get_field( 'anio_vehiculo', $pid );
		$km          = get_field( 'kilometraje_vehiculo', $pid );
		$transmision = get_field( 'transmision_vehiculo', $pid );
		?>

		<article <?php post_class( 'mm-ficha mm-ficha--vehiculo' ); ?>>

			<header class="mm-ficha-head">
				<div class="mm-badges">
					<?php if ( $tipo ) : ?><span class="mm-badge"><?php echo esc_html( $tipo ); ?></span><?php endif; ?>
					<?php if ( $anio ) : ?><span class="mm-badge mm-badge--op"><?php echo esc_html( $anio ); ?></span><?php endif; ?>
				</div>
				<h1 class="mm-title"><?php the_title(); ?></h1>
			</header>

			<div class="mm-ficha-grid">

				<div class="mm-main">
					<?php echo marymed_gallery_html( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

					<div class="mm-spec">
						<?php if ( $anio ) : ?>
							<div class="mm-spec__item"><strong><?php echo esc_html( $anio ); ?></strong><small><?php esc_html_e( 'Anio', 'marymed' ); ?></small></div>
						<?php endif; ?>
						<?php if ( '' !== $km ) : ?>
							<div class="mm-spec__item"><strong><?php echo esc_html( number_format( (float) $km ) ); ?> km</strong><small><?php esc_html_e( 'Kilometraje', 'marymed' ); ?></small></div>
						<?php endif; ?>
						<?php if ( $transmision ) : ?>
							<div class="mm-spec__item"><strong><?php echo esc_html( $transmision ); ?></strong><small><?php esc_html_e( 'Transmision', 'marymed' ); ?></small></div>
						<?php endif; ?>
						<?php if ( $tipo ) : ?>
							<div class="mm-spec__item"><strong><?php echo esc_html( $tipo ); ?></strong><small><?php esc_html_e( 'Tipo', 'marymed' ); ?></small></div>
						<?php endif; ?>
					</div>

					<div class="mm-content">
						<?php the_content(); ?>
					</div>

					<?php echo marymed_tiktok_embed( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>

				<aside class="mm-side">
					<div class="mm-side-card">
						<?php if ( $precio ) : ?>
							<div class="mm-precio">$<?php echo esc_html( number_format( $precio ) ); ?> <span>USD</span></div>
						<?php endif; ?>

						<?php echo marymed_wa_chat_link( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo marymed_share_buttons( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</aside>

			</div>
		</article>

		<?php
	endwhile;

	echo marymed_wa_float( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput
	?>

</div>

<?php
get_footer();
