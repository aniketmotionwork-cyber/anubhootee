<?php
/**
 * Next story: the previous (older) post, or the newer one if this is the oldest.
 *
 * @package Anubhootee
 */

$anu_id = anu_block_post_id( isset( $block ) ? $block : null );
if ( ! $anu_id ) {
	return;
}
$anu_post = get_post( $anu_id );
$GLOBALS['post'] = $anu_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- adjacent-post helpers read the global.
setup_postdata( $anu_post );
$anu_next = get_previous_post();
if ( ! $anu_next ) {
	$anu_next = get_next_post();
}
wp_reset_postdata();
if ( ! $anu_next ) {
	return;
}
$anu_cats = get_the_category( $anu_next->ID );
$anu_kick = anu_t( 'next_story' ) . ( $anu_cats ? ' · ' . $anu_cats[0]->name : '' );
?>
<a <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-next alignfull' ) ); // phpcs:ignore ?> href="<?php echo esc_url( get_permalink( $anu_next ) ); ?>">
	<?php
	if ( has_post_thumbnail( $anu_next ) ) {
		echo get_the_post_thumbnail( $anu_next, 'full', array( 'loading' => 'lazy', 'sizes' => '100vw', 'alt' => '' ) );
	}
	?>
	<span class="anu-next__inner">
		<small><?php echo esc_html( $anu_kick ); ?></small>
		<span class="anu-next__title"><?php echo esc_html( get_the_title( $anu_next ) ); ?> <span class="anu-next__arrow" aria-hidden="true">→</span></span>
	</span>
</a>
