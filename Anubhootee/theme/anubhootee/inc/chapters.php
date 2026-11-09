<?php
/**
 * Chapters: categories that appear in the homepage showcase.
 *
 * Posts → Categories → edit a category → "Homepage chapter" fields:
 * big title, Devanagari word, button text, cover photo, photo credit, order, show on homepage.
 *
 * @package Anubhootee
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta keys and their sanitizers.
 *
 * @return array
 */
function anu_chapter_fields() {
	return array(
		'anu_show'     => 'absint',
		'anu_order'    => 'intval',
		'anu_title'    => 'sanitize_text_field',
		'anu_deva'     => 'sanitize_text_field',
		'anu_cta'      => 'sanitize_text_field',
		'anu_cover'    => 'absint',
		'anu_credit'   => 'sanitize_text_field',
		'anu_link'     => 'esc_url_raw',
	);
}

/**
 * Register term meta (also exposes it to the REST API).
 */
function anu_register_chapter_meta() {
	foreach ( anu_chapter_fields() as $key => $sanitize ) {
		register_term_meta(
			'category',
			$key,
			array(
				'type'              => in_array( $sanitize, array( 'absint', 'intval' ), true ) ? 'integer' : 'string',
				'single'            => true,
				'show_in_rest'      => true,
				// Wrapped: WP passes 4 args, and PHP 8 built-ins like intval() reject extras.
				'sanitize_callback' => function ( $value ) use ( $sanitize ) {
					return call_user_func( $sanitize, $value );
				},
			)
		);
	}
}
add_action( 'init', 'anu_register_chapter_meta' );

/**
 * Field markup, shared by the "add" and "edit" screens.
 *
 * @param WP_Term|null $term Term.
 * @param bool         $table Use the edit-screen table layout.
 */
function anu_chapter_form( $term = null, $table = false ) {
	$get   = function ( $key ) use ( $term ) {
		return $term ? get_term_meta( $term->term_id, $key, true ) : '';
	};
	$cover = (int) $get( 'anu_cover' );
	$thumb = $cover ? wp_get_attachment_image_url( $cover, 'medium' ) : '';

	$rows = array(
		'anu_show'   => array( __( 'Show on homepage', 'anubhootee' ), '<label><input type="checkbox" name="anu_show" value="1" ' . checked( (int) $get( 'anu_show' ), 1, false ) . '> ' . esc_html__( 'Show this category as a chapter in the homepage showcase', 'anubhootee' ) . '</label>' ),
		'anu_order'  => array( __( 'Chapter order', 'anubhootee' ), '<input type="number" name="anu_order" value="' . esc_attr( $get( 'anu_order' ) ) . '" style="width:6em"> <span class="description">' . esc_html__( '1 = first', 'anubhootee' ) . '</span>' ),
		'anu_title'  => array( __( 'Big title', 'anubhootee' ), '<input type="text" name="anu_title" value="' . esc_attr( $get( 'anu_title' ) ) . '" placeholder="Rasta"><p class="description">' . esc_html__( 'Large word on the homepage, e.g. Rasta, Chav, Utsav. Leave empty to use the category name.', 'anubhootee' ) . '</p>' ),
		'anu_deva'   => array( __( 'Devanagari word', 'anubhootee' ), '<input type="text" name="anu_deva" value="' . esc_attr( $get( 'anu_deva' ) ) . '" placeholder="रस्ता" lang="mr">' ),
		'anu_cta'    => array( __( 'Button text', 'anubhootee' ), '<input type="text" name="anu_cta" value="' . esc_attr( $get( 'anu_cta' ) ) . '" placeholder="Explore routes">' ),
		'anu_cover'  => array(
			__( 'Cover photo', 'anubhootee' ),
			'<input type="hidden" name="anu_cover" class="anu-cover-id" value="' . esc_attr( $cover ) . '">'
			. '<div class="anu-cover-preview" style="margin-bottom:8px">' . ( $thumb ? '<img src="' . esc_url( $thumb ) . '" style="max-width:260px;height:auto;display:block">' : '' ) . '</div>'
			. '<button type="button" class="button anu-cover-pick">' . esc_html__( 'Choose photo', 'anubhootee' ) . '</button> '
			. '<button type="button" class="button-link anu-cover-clear">' . esc_html__( 'Remove', 'anubhootee' ) . '</button>'
			. '<p class="description">' . esc_html__( 'Landscape, at least 1920px wide.', 'anubhootee' ) . '</p>',
		),
		'anu_credit' => array( __( 'Photo credit', 'anubhootee' ), '<input type="text" name="anu_credit" value="' . esc_attr( $get( 'anu_credit' ) ) . '" placeholder="Anubhootee"><p class="description">' . esc_html__( 'Optional. Required if the photo is not your own.', 'anubhootee' ) . '</p>' ),
		'anu_link'   => array( __( 'Button link', 'anubhootee' ), '<input type="url" name="anu_link" value="' . esc_attr( $get( 'anu_link' ) ) . '" placeholder="https://…/shop/"><p class="description">' . esc_html__( 'Optional. Where the button goes, e.g. the Shop page. Leave empty to open this category.', 'anubhootee' ) . '</p>' ),
	);

	wp_nonce_field( 'anu_chapter_save', 'anu_chapter_nonce' );

	if ( $table ) {
		echo '<tr class="form-field"><th colspan="2"><h2 style="margin:24px 0 0">' . esc_html__( 'Homepage chapter', 'anubhootee' ) . '</h2></th></tr>';
		foreach ( $rows as $key => $row ) {
			echo '<tr class="form-field"><th scope="row"><label>' . esc_html( $row[0] ) . '</label></th><td>' . $row[1] . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts above.
		}
	} else {
		echo '<h2>' . esc_html__( 'Homepage chapter', 'anubhootee' ) . '</h2>';
		foreach ( $rows as $key => $row ) {
			echo '<div class="form-field"><label>' . esc_html( $row[0] ) . '</label>' . $row[1] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
}
add_action( 'category_add_form_fields', function () { anu_chapter_form( null, false ); } );
add_action( 'category_edit_form_fields', function ( $term ) { anu_chapter_form( $term, true ); } );

/**
 * Save fields.
 *
 * @param int $term_id Term ID.
 */
function anu_chapter_save( $term_id ) {
	if ( ! isset( $_POST['anu_chapter_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['anu_chapter_nonce'] ) ), 'anu_chapter_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	foreach ( anu_chapter_fields() as $key => $sanitize ) {
		$value = isset( $_POST[ $key ] ) ? call_user_func( $sanitize, wp_unslash( $_POST[ $key ] ) ) : ( 'anu_show' === $key ? 0 : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( '' === $value ) {
			delete_term_meta( $term_id, $key );
		} else {
			update_term_meta( $term_id, $key, $value );
		}
	}
}
add_action( 'created_category', 'anu_chapter_save' );
add_action( 'edited_category', 'anu_chapter_save' );

/**
 * Media picker on category screens.
 *
 * @param string $hook Admin page.
 */
function anu_chapter_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'category' !== $screen->taxonomy ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'anubhootee-chapters-admin', ANU_URI . '/assets/js/admin-chapters.js', array( 'jquery' ), ANU_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'anu_chapter_admin_assets' );

/**
 * Chapters for the showcase. Falls back to built-in demo chapters until real ones are set.
 *
 * @return array[] Each: title, deva, tag, cta, url, img, w, h, credit.
 */
function anu_get_chapters() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array( 'key' => 'anu_show', 'value' => 1 ),
			),
		)
	);

	$chapters = array();
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$cover = (int) get_term_meta( $term->term_id, 'anu_cover', true );
			$src   = $cover ? wp_get_attachment_image_src( $cover, '2048x2048' ) : false;
			if ( ! $src && $cover ) {
				$src = wp_get_attachment_image_src( $cover, 'full' );
			}
			if ( ! $src ) {
				continue; // A chapter needs a photo.
			}
			$title      = get_term_meta( $term->term_id, 'anu_title', true );
			$cta        = get_term_meta( $term->term_id, 'anu_cta', true );
			$link       = (string) get_term_meta( $term->term_id, 'anu_link', true );
			$chapters[] = array(
				'order'  => (int) get_term_meta( $term->term_id, 'anu_order', true ),
				'title'  => $title ? $title : $term->name,
				'deva'   => (string) get_term_meta( $term->term_id, 'anu_deva', true ),
				'tag'    => $term->name,
				'cta'    => $cta ? $cta : anu_t( 'explore' ),
				'url'    => $link ? $link : get_term_link( $term ),
				'img'    => $src[0],
				'w'      => (int) $src[1],
				'h'      => (int) $src[2],
				'credit' => (string) get_term_meta( $term->term_id, 'anu_credit', true ),
			);
		}
		usort(
			$chapters,
			function ( $a, $b ) {
				return $a['order'] - $b['order'];
			}
		);
	}

	if ( empty( $chapters ) ) {
		$chapters = anu_demo_chapters();
	}

	return apply_filters( 'anu_chapters', $chapters );
}

/**
 * Demo chapters using the bundled placeholder photos (Wikimedia Commons, CC BY-SA 4.0).
 *
 * @return array[]
 */
function anu_demo_chapters() {
	$mr   = anu_is_marathi();
	$img  = ANU_URI . '/assets/img/';
	$home = home_url( '/' );
	$demo = array(
		array( 'Rasta', 'रस्ता', 'Road Travel', 'प्रवास', 'Explore routes', 'मार्ग पहा', 'road.jpg', 1920, 1440, 'Mariappan Ramachandran · CC BY-SA 4.0' ),
		array( 'Chav', 'चव', 'Food', 'खाद्य', 'Taste stories', 'चवीच्या गोष्टी', 'food.jpg', 1920, 1280, 'Ankushkr2020 · CC BY-SA 4.0' ),
		array( 'Utsav', 'उत्सव', 'Festivals & Culture', 'सण आणि संस्कृती', 'Feel the culture', 'संस्कृती अनुभवा', 'utsav.jpg', 1920, 1280, 'Achary K Krishnan · CC BY-SA 4.0' ),
		array( 'Goshti', 'गोष्टी', 'Stories & Diaries', 'गोष्टी आणि डायरी', 'Read stories', 'गोष्टी वाचा', 'ghat.jpg', 1920, 1272, 'Rangan Datta Wiki · CC BY-SA 4.0' ),
		array( 'Kala', 'कला', 'Art & Handicrafts', 'कला आणि हस्तकला', 'Visit the shop', 'दुकानात जा', 'kala.jpg', 1920, 1440, 'PratishKhedekar · CC BY-SA 4.0' ),
		array( 'Chitrapat', 'चित्रपट', 'Documentaries & Vlogs', 'माहितीपट आणि व्लॉग्स', 'Watch films', 'चित्रपट पहा', 'film.jpg', 1920, 1183, 'Clément Bardot · CC BY-SA 4.0' ),
	);
	$out = array();
	foreach ( $demo as $i => $d ) {
		$out[] = array(
			'order'  => $i + 1,
			'title'  => $d[0],
			'deva'   => $d[1],
			'tag'    => $mr ? $d[3] : $d[2],
			'cta'    => $mr ? $d[5] : $d[4],
			'url'    => $home,
			'img'    => $img . $d[6],
			'w'      => $d[7],
			'h'      => $d[8],
			'credit' => $d[9],
		);
	}
	return $out;
}
