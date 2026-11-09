<?php
/**
 * Theme blocks (server-rendered).
 *
 * @package Anubhootee
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register scripts/styles, then the blocks that reference them by handle.
 */
function anu_register_blocks() {
	wp_register_script(
		'anubhootee-blocks-editor',
		ANU_URI . '/assets/js/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n' ),
		ANU_VERSION,
		true
	);
	wp_register_style( 'anubhootee-showcase', ANU_URI . '/assets/css/showcase.css', array(), ANU_VERSION );
	wp_register_script( 'anubhootee-showcase', ANU_URI . '/assets/js/showcase.js', array(), ANU_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );

	foreach ( array( 'showcase', 'story-hero', 'byline', 'next-story', 'logo', 'lang-switch', 'copyright', 'policy-links' ) as $block ) {
		register_block_type( ANU_DIR . '/blocks/' . $block );
	}
}
add_action( 'init', 'anu_register_blocks' );

/**
 * Resolve the post a block is rendering for.
 *
 * @param WP_Block|null $block Block instance.
 * @return int
 */
function anu_block_post_id( $block ) {
	if ( $block instanceof WP_Block && ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}
	return (int) get_the_ID();
}
