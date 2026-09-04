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
	<section class="mm-hero">
		<div class="mm-container">
			<h1 class="mm-hero__title"><?php esc_html_e( 'Encuentra tu propiedad o tu proximo auto', 'marymed' ); ?></h1>
			<p class="mm-hero__sub"><?php esc_html_e( 'Lotes, casas, departamentos, edificios y vehiculos verificados. Atencion directa por WhatsApp.', 'marymed' ); ?></p>
			<div class="mm-hero__cta">
				<a class="mm-btn" href="<?php echo esc_url( get_post_type_archive_link( 'propiedades' ) ); ?>"><?php esc_html_e( 'Ver Propiedades', 'marymed' ); ?></a>
				<a class="mm-btn mm-btn--wa" href="<?php echo esc_url( get_post_type_archive_link( 'vehiculos' ) ); ?>"><?php esc_html_e( 'Ver Vehiculos', 'marymed' ); ?></a>
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
