<?php
/**
 * Local test only: the main menu (used by the header and footer navigation blocks).
 * Links point at categories and pages by ID, so they stay correct if slugs change.
 */
require_once '/wordpress/wp-load.php';
wp_set_current_user( 1 );

function anu_nav_cat( $name, $label ) {
	$t = get_term_by( 'name', $name, 'category' );
	if ( ! $t ) {
		return '';
	}
	$attrs = array( 'label' => $label, 'type' => 'category', 'id' => $t->term_id, 'url' => get_term_link( $t ), 'kind' => 'taxonomy' );
	return '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
}
function anu_nav_page( $id, $label ) {
	if ( ! $id || $id < 1 ) {
		return '';
	}
	$attrs = array( 'label' => $label, 'type' => 'page', 'id' => (int) $id, 'url' => get_permalink( $id ), 'kind' => 'post-type' );
	return '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->';
}

$about = get_page_by_path( 'about' );
$shop  = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;

$items = array(
	anu_nav_cat( 'Stories & Diaries', 'Stories' ),
	anu_nav_cat( 'Road Travel', 'Road Travel' ),
	anu_nav_cat( 'Food', 'Food' ),
	anu_nav_cat( 'Festivals & Culture', 'Culture' ),
	anu_nav_cat( 'Art & Handicrafts', 'Art' ),
	anu_nav_cat( 'Documentaries & Vlogs', 'Films' ),
	anu_nav_page( $shop, 'Shop' ),
	anu_nav_page( $about ? $about->ID : 0, 'About' ),
);

$existing = get_posts( array( 'post_type' => 'wp_navigation', 'numberposts' => -1, 'post_status' => 'any', 'fields' => 'ids' ) );
foreach ( $existing as $id ) {
	wp_delete_post( $id, true );
}
wp_insert_post(
	array(
		'post_type'    => 'wp_navigation',
		'post_title'   => 'Main menu',
		'post_status'  => 'publish',
		'post_content' => implode( "\n", array_filter( $items ) ),
	)
);
echo 'ANU_MENU: ' . count( array_filter( $items ) ) . ' items';
