<?php
/**
 * Helpers sociales: WhatsApp, TikTok Embed, botones de compartir.
 *
 * @package Marymed
 */

defined( 'ABSPATH' ) || exit;

/* ============================================================
 * WHATSAPP
 * ========================================================== */

/**
 * Numero de WhatsApp (codigo de pais + numero, solo digitos).
 * Prioridad: constante MARYMED_WHATSAPP_NUMBER > Customizer.
 */
function marymed_whatsapp_number() {
	if ( defined( 'MARYMED_WHATSAPP_NUMBER' ) && MARYMED_WHATSAPP_NUMBER ) {
		$number = MARYMED_WHATSAPP_NUMBER;
	} else {
		$number = get_theme_mod( 'marymed_whatsapp', '' );
	}
	return preg_replace( '/[^0-9]/', '', (string) $number );
}

/**
 * Construye el mensaje comercial: "Hola, me interesa {título} - {URL}".
 * Devuelve '' si no hay numero configurado.
 *
 * @param int $post_id ID del post (propiedad o vehiculo).
 * @return string URL de wa.me lista para <a href>.
 */
function marymed_wa_chat_url( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$number  = marymed_whatsapp_number();

	if ( ! $post_id || ! $number ) {
		return '';
	}

	$title = get_the_title( $post_id );
	$url   = get_permalink( $post_id );
	$text  = sprintf(
		'Hola Marymed, me interesa "%1$s". Pueden darme mas informacion? %2$s',
		$title,
		$url
	);

	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $text );
}

/**
 * <a> estandar para botones en grid/ficha.
 */
function marymed_wa_chat_link( $post_id = 0 ) {
	$href = marymed_wa_chat_url( $post_id );
	if ( ! $href ) {
		return '';
	}
	return sprintf(
		'<a class="marymed-wa-btn" href="%1$s" target="_blank" rel="noopener nofollow">%2$s</a>',
		esc_url( $href ),
		esc_html__( 'Consultar por WhatsApp', 'marymed' )
	);
}

/**
 * Boton flotante de WhatsApp (se usa en la ficha via shortcode o code
 * element de Bricks). Jala titulo + enlace de la propiedad/vehiculo.
 */
function marymed_wa_float( $post_id = 0 ) {
	$href = marymed_wa_chat_url( $post_id );
	if ( ! $href ) {
		return '';
	}
	return sprintf(
		'<a class="marymed-wa-float" href="%1$s" target="_blank" rel="noopener nofollow" aria-label="%2$s">%3$s</a>',
		esc_url( $href ),
		esc_attr__( 'WhatsApp', 'marymed' ),
		esc_html__( 'WhatsApp', 'marymed' )
	);
}
add_shortcode( 'marymed_whatsapp_float', 'marymed_wa_float' );

/* ============================================================
 * TIKTOK EMBED
 * ========================================================== */

/**
 * Devuelve la URL del video TikTok segun el CPT.
 *
 * @param int $post_id ID del post.
 * @return string URL o '' si vacio.
 */
function marymed_tiktok_field_url( $post_id = 0 ) {
	$post_id   = $post_id ? $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );

	if ( 'vehiculos' === $post_type ) {
		$field = 'url_tiktok_vehiculo';
	} else {
		$field = 'url_tiktok_propiedad';
	}

	if ( function_exists( 'get_field' ) ) {
		return (string) get_field( $field, $post_id );
	}
	return '';
}

/**
 * Extrae el ID numerico de un URL de TikTok: /@user/video/7373838...
 */
function marymed_tiktok_video_id( $url ) {
	if ( preg_match( '#tiktok\.com/.+/video/(\d+)#i', $url, $m ) ) {
		return $m[1];
	}
	return '';
}

/**
 * HTML de embed oficial de TikTok. Si no hay URL devuelve '' para que el
 * contenedor del builder pueda ocultarse de forma condicional.
 *
 * @param int $post_id ID del post.
 * @return string Blockquote + script embed de TikTok (o '').
 */
function marymed_tiktok_embed( $post_id = 0 ) {
	$url = marymed_tiktok_field_url( $post_id );
	$id  = marymed_tiktok_video_id( $url );

	if ( ! $url || ! $id ) {
		return '';
	}

	return sprintf(
		'<blockquote class="tiktok-embed marymed-tiktok-embed" cite="%1$s" data-video-id="%2$s" data-embed-from="oembed" style="max-width:605px;min-width:325px;"><section><a target="_blank" rel="noopener" title="%3$s" href="%1$s">%3$s</a></section></blockquote><script async src="https://www.tiktok.com/embed.js"></script>',
		esc_url( $url ),
		esc_attr( $id ),
		esc_attr( get_the_title( $post_id ) )
	);
}
add_shortcode( 'marymed_tiktok', 'marymed_tiktok_embed' );

/* ============================================================
 * BOTONES DE COMPARTIR (grid)
 * ========================================================== */

/**
 * Compartir rapido movil: WhatsApp, Facebook, Telegram.
 *
 * @param int $post_id ID del post.
 * @return string HTML de los 3 enlaces.
 */
function marymed_share_buttons( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$url   = rawurlencode( get_permalink( $post_id ) );
	$title = rawurlencode( get_the_title( $post_id ) );

	$links = array(
		'wa' => array(
			'href' => 'https://wa.me/?text=' . $title . '%20' . $url,
			'label' => __( 'WhatsApp', 'marymed' ),
		),
		'fb' => array(
			'href' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
			'label' => __( 'Facebook', 'marymed' ),
		),
		'tg' => array(
			'href' => 'https://t.me/share/url?url=' . $url . '&text=' . $title,
			'label' => __( 'Telegram', 'marymed' ),
		),
	);

	$html = '<div class="marymed-share" aria-label="' . esc_attr__( 'Compartir', 'marymed' ) . '">';
	foreach ( $links as $key => $link ) {
		$html .= sprintf(
			'<a class="ms-%1$s" href="%2$s" target="_blank" rel="noopener nofollow">%3$s</a>',
			esc_attr( $key ),
			esc_url( $link['href'] ),
			esc_html( $link['label'] )
		);
	}
	$html .= '</div>';

	return $html;
}
add_shortcode( 'marymed_share', 'marymed_share_buttons' );
