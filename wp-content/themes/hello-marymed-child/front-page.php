<?php
/**
 * Portada (front-page.php) - Portal Marymed Real Estate.
 *
 * Muestra hero + propiedades recientes + vehiculos recientes usando las
 * tarjetas de template-parts. Si luego quieres una home con Elementor,
 * crea la pagina y ajusta Lectura > "Una pagina estatica".
 *
 * @package Marymed
 */

get_header();
?>

<div class="mm-home">

	<!-- ============================ HERO ============================ -->
	<?php
	$hero_image = get_theme_mod( 'marymed_hero_image', '' );
	$hero_style = $hero_image
		? ' style="background-image:url(' . esc_url( $hero_image ) . ');"'
		: '';
	?>
	<section class="mm-hero<?php echo $hero_image ? ' has-image' : ''; ?>"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<div class="mm-container">
			<p class="mm-kicker"><?php esc_html_e( 'Marymed Real Estate', 'marymed' ); ?></p>
			<h1 class="mm-hero__title"><?php esc_html_e( 'Encuentra tu propiedad o tu proximo auto', 'marymed' ); ?></h1>
			<p class="mm-hero__sub"><?php esc_html_e( 'Lotes, casas, departamentos, edificios y vehiculos verificados. Atencion directa por WhatsApp.', 'marymed' ); ?></p>
			<div class="mm-hero__cta">
				<a class="mm-btn mm-btn--solid" href="<?php echo esc_url( get_post_type_archive_link( 'propiedades' ) ); ?>"><?php esc_html_e( 'Ver Propiedades', 'marymed' ); ?></a>
				<a class="mm-btn mm-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'vehiculos' ) ); ?>"><?php esc_html_e( 'Ver Vehiculos', 'marymed' ); ?></a>
			</div>
		</div>
	</section>

	<!-- ===================== VENTAJAS / SERVICIOS ===================== -->
	<section class="mm-feats">
		<div class="mm-container">
			<h2 class="mm-feats__title"><?php esc_html_e( 'Por que Marymed', 'marymed' ); ?></h2>
			<div class="mm-feats__grid">
				<div class="mm-feat">
					<span class="mm-feat__icon"><?php // phpcs:ignore WordPress.Security.EscapeOutput
						echo '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.1-7-11a7 7 0 1 1 14 0c0 5.9-7 11-7 11z"/><circle cx="12" cy="10" r="2.6"/></svg>';
					?></span>
					<h4><?php esc_html_e( 'Mapa y ubicacion real', 'marymed' ); ?></h4>
					<p><?php esc_html_e( 'Cada publicacion muestra su geolocalizacion en un mapa interactivo.', 'marymed' ); ?></p>
				</div>
				<div class="mm-feat">
					<span class="mm-feat__icon"><?php // phpcs:ignore WordPress.Security.EscapeOutput
						echo '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="16" rx="2.5"/><path d="M10.2 8.8l4.6 3.2-4.6 3.2z" fill="currentColor" stroke="none"/></svg>';
					?></span>
					<h4><?php esc_html_e( 'Video real por ficha', 'marymed' ); ?></h4>
					<p><?php esc_html_e( 'Recorridos y fichas en TikTok integrados en cada publicacion.', 'marymed' ); ?></p>
				</div>
				<div class="mm-feat">
					<span class="mm-feat__icon"><?php // phpcs:ignore WordPress.Security.EscapeOutput
						echo '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-8 8H4.5l2.4-2.8A8 8 0 1 1 21 12z"/><path d="M8.5 11h7M8.5 14h4.5"/></svg>';
					?></span>
					<h4><?php esc_html_e( 'Atencion inmediata', 'marymed' ); ?></h4>
					<p><?php esc_html_e( 'Consulta por WhatsApp y te respondemos con fotos y mas detalles.', 'marymed' ); ?></p>
				</div>
				<div class="mm-feat">
					<span class="mm-feat__icon"><?php // phpcs:ignore WordPress.Security.EscapeOutput
						echo '<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.4-2.9 7.8-7 9.5C7.9 18.8 5 15.4 5 11V6z"/><path d="M9.2 12l2 2 3.6-4"/></svg>';
					?></span>
					<h4><?php esc_html_e( 'Catalogos claros', 'marymed' ); ?></h4>
					<p><?php esc_html_e( 'Filtros rapidos por tipo, zona y precio con resultados al instante.', 'marymed' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<?php
	// ======================= PROPIEDADES =======================
	$prop_query = new WP_Query(
		array(
			'post_type'      => 'propiedades',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
	if ( $prop_query->have_posts() ) :
		?>
		<section class="mm-home-section">
			<div class="mm-container">
				<div class="mm-section-head">
					<h2><?php esc_html_e( 'Propiedades destacadas', 'marymed' ); ?></h2>
					<a class="mm-btn mm-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'propiedades' ) ); ?>"><?php esc_html_e( 'Ver todas', 'marymed' ); ?></a>
				</div>
				<div class="mm-grid">
					<?php
					while ( $prop_query->have_posts() ) :
						$prop_query->the_post();
						get_template_part( 'template-parts/card', 'propiedad' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// ======================== VEHICULOS ========================
	$veh_query = new WP_Query(
		array(
			'post_type'      => 'vehiculos',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
	if ( $veh_query->have_posts() ) :
		?>
		<section class="mm-home-section mm-home-section--alt">
			<div class="mm-container">
				<div class="mm-section-head">
					<h2><?php esc_html_e( 'Autos y motores recientes', 'marymed' ); ?></h2>
					<a class="mm-btn mm-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'vehiculos' ) ); ?>"><?php esc_html_e( 'Ver todos', 'marymed' ); ?></a>
				</div>
				<div class="mm-grid">
					<?php
					while ( $veh_query->have_posts() ) :
						$veh_query->the_post();
						get_template_part( 'template-parts/card', 'vehiculo' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	// ========================= TIKTOK =========================
	$tiktok_feed = marymed_tiktok_feed_html();
	if ( $tiktok_feed ) :
		?>
		<section class="mm-home-section">
			<div class="mm-container">
				<div class="mm-section-head">
					<h2><?php esc_html_e( 'Lo ultimo en TikTok', 'marymed' ); ?></h2>
				</div>
				<?php echo $tiktok_feed; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ======================= CTA WHATSAPP ======================= -->
	<section class="mm-cta">
		<div class="mm-container">
			<h2><?php esc_html_e( 'Consulta disponibilidad por WhatsApp', 'marymed' ); ?></h2>
			<p><?php esc_html_e( 'Responde en minutos con fotos, videos y recorridos.', 'marymed' ); ?></p>
			<?php echo marymed_wa_cta_link(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>

</div>

<?php
get_footer();
