<?php
/**
 * Plantilla single del CPT `propiedades`.
 *
 * Ficha de inmueble con datos ACF, galeria nativa (the_content), video
 * TikTok condicional, recorrido 3D, mapa Leaflet y WhatsApp flotante.
 *
 * @package Marymed
 */

get_header();
?>

<div class="mm-container">

	<?php
	while ( have_posts() ) :
		the_post();
		$pid  = get_the_ID();
		$tipo = get_field( 'tipo_inmueble', $pid );
		$op   = get_field( 'tipo_operacion', $pid );
		$usd  = (float) get_field( 'precio_usd', $pid );
		$pen  = (float) get_field( 'precio_pen', $pid );
		$area = get_field( 'area_total', $pid );
		$loc  = marymed_location_data( $pid );
		?>

		<article <?php post_class( 'mm-ficha' ); ?>>

			<header class="mm-ficha-head">
				<div class="mm-badges">
					<?php if ( $tipo ) : ?><span class="mm-badge"><?php echo esc_html( $tipo ); ?></span><?php endif; ?>
					<?php if ( $op ) : ?><span class="mm-badge mm-badge--op"><?php echo esc_html( $op ); ?></span><?php endif; ?>
				</div>
				<h1 class="mm-title"><?php the_title(); ?></h1>
				<?php if ( ! empty( $loc['zona'] ) || ! empty( $loc['direccion'] ) ) : ?>
					<p class="mm-ubi"><?php echo esc_html( trim( ( $loc['direccion'] ?? '' ) . ' - ' . ( $loc['zona'] ?? '' ), ' -' ) ); ?></p>
				<?php endif; ?>
			</header>

			<div class="mm-ficha-grid">

				<div class="mm-main">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="mm-featured"><?php the_post_thumbnail( 'large' ); ?></figure>
					<?php endif; ?>

					<div class="mm-content">
						<?php
						// Contenido del editor: galeria nativa de WP + descripcion.
						the_content();
						?>
					</div>

					<?php
					// Video TikTok: solo si el campo tiene URL.
					echo marymed_tiktok_embed( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput

					// Recorrido 3D (iframe Earth Studio, etc.): solo si hay codigo.
					echo marymed_recorrido_3d( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput

					// Mapa Leaflet gratuito.
					echo marymed_leaflet_map( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput
					?>
				</div>

				<aside class="mm-side">
					<div class="mm-side-card">
						<?php if ( $usd ) : ?>
							<div class="mm-precio">$<?php echo esc_html( number_format( $usd ) ); ?> <span>USD</span></div>
						<?php endif; ?>
						<?php if ( $pen ) : ?>
							<div class="mm-precio-pen">S/ <?php echo esc_html( number_format( $pen, 2 ) ); ?></div>
						<?php endif; ?>

						<div class="mm-spec">
							<?php if ( $area ) : ?>
								<div class="mm-spec__item"><strong><?php echo esc_html( $area ); ?> m2</strong><small><?php esc_html_e( 'Area', 'marymed' ); ?></small></div>
							<?php endif; ?>
							<?php if ( $tipo ) : ?>
								<div class="mm-spec__item"><strong><?php echo esc_html( $tipo ); ?></strong><small><?php esc_html_e( 'Tipo', 'marymed' ); ?></small></div>
							<?php endif; ?>
							<?php if ( $op ) : ?>
								<div class="mm-spec__item"><strong><?php echo esc_html( $op ); ?></strong><small><?php esc_html_e( 'Operacion', 'marymed' ); ?></small></div>
							<?php endif; ?>
						</div>

						<?php echo marymed_wa_chat_link( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo marymed_share_buttons( $pid ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</aside>

			</div>
		</article>

		<?php
	endwhile;

	// Boton flotante de WhatsApp con titulo + enlace de esta ficha.
	echo marymed_wa_float( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput
	?>

</div>

<?php
get_footer();
