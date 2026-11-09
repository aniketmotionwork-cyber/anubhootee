<?php
/**
 * Anubhootee theme functions.
 *
 * @package Anubhootee
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANU_VERSION', '0.1.0' );
define( 'ANU_DIR', get_template_directory() );
define( 'ANU_URI', get_template_directory_uri() );

require_once ANU_DIR . '/inc/i18n.php';
require_once ANU_DIR . '/inc/chapters.php';
require_once ANU_DIR . '/inc/blocks.php';

/**
 * Theme setup.
 */
function anu_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_editor_style( array( anu_fonts_url(), 'assets/css/theme.css' ) );
	load_theme_textdomain( 'anubhootee', ANU_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'anu_setup' );

/**
 * Google Fonts: Inter (Latin), Mukta + Tiro Devanagari Marathi (Marathi), Newsreader (quotes).
 */
function anu_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Mukta:wght@300;400;500;700&family=Tiro+Devanagari+Marathi&family=Newsreader:ital,opsz,wght@1,6..72,400&display=swap';
}

/**
 * Front-end assets.
 */
function anu_enqueue() {
	wp_enqueue_style( 'anubhootee-fonts', anu_fonts_url(), array(), null );
	wp_enqueue_style( 'anubhootee', ANU_URI . '/assets/css/theme.css', array( 'anubhootee-fonts' ), ANU_VERSION );
	wp_enqueue_script( 'anubhootee', ANU_URI . '/assets/js/theme.js', array(), ANU_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style( 'anubhootee-store', ANU_URI . '/assets/css/store.css', array( 'anubhootee' ), ANU_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'anu_enqueue' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function anu_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'anu_resource_hints', 10, 2 );

/**
 * Body classes: mark Marathi pages so Devanagari is never letter-spaced.
 *
 * @param array $classes Classes.
 * @return array
 */
function anu_body_class( $classes ) {
	if ( anu_is_marathi() ) {
		$classes[] = 'anu-lang-mr';
	}
	return $classes;
}
add_filter( 'body_class', 'anu_body_class' );

/**
 * Estimated reading time in minutes.
 *
 * @param int|null $post_id Post ID.
 * @return int
 */
function anu_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id );
	$words   = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( strip_shortcodes( $content ) ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Optional "location" field on posts (e.g. "Western Ghats"), shown in the story header.
 */
function anu_register_meta() {
	register_post_meta(
		'post',
		'anu_location',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'anu_register_meta' );

/**
 * Store: when an order qualifies for free delivery, offer only that (hide paid rates).
 *
 * @param array $rates Shipping rates.
 * @return array
 */
function anu_free_shipping_only( $rates ) {
	$free = array_filter(
		$rates,
		function ( $rate ) {
			return 'free_shipping' === $rate->get_method_id();
		}
	);
	return $free ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'anu_free_shipping_only', 100 );

/**
 * Block patterns category.
 */
function anu_pattern_category() {
	register_block_pattern_category( 'anubhootee', array( 'label' => __( 'Anubhootee', 'anubhootee' ) ) );
}
add_action( 'init', 'anu_pattern_category' );
