<?php
/**
 * Byline: avatar, author, date, share links.
 *
 * @package Anubhootee
 */

$anu_id = anu_block_post_id( isset( $block ) ? $block : null );
if ( ! $anu_id ) {
	return;
}
$anu_author = (int) get_post_field( 'post_author', $anu_id );
$anu_name   = get_the_author_meta( 'display_name', $anu_author );
$anu_url    = get_permalink( $anu_id );
$anu_title  = get_the_title( $anu_id );
$anu_date   = anu_deva_digits( get_the_date( 'j F Y', $anu_id ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-byline' ) ); // phpcs:ignore ?>>
	<div class="anu-byline__who">
		<div class="anu-byline__avatar" aria-hidden="true">अ</div>
		<div>
			<b><?php echo esc_html( $anu_name ? $anu_name : get_bloginfo( 'name' ) ); ?></b>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $anu_id ) ); ?>"><?php echo esc_html( $anu_date ); ?></time>
		</div>
	</div>
	<div class="anu-share">
		<a href="<?php echo esc_url( 'https://wa.me/?text=' . rawurlencode( $anu_title . ' ' . $anu_url ) ); ?>" target="_blank" rel="noopener">WhatsApp</a>
		<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $anu_url ) ); ?>" target="_blank" rel="noopener">Facebook</a>
		<button type="button" data-anu-copy="<?php echo esc_url( $anu_url ); ?>" data-copied="<?php echo esc_attr( anu_t( 'copied' ) ); ?>"><?php echo esc_html( anu_t( 'copy_link' ) ); ?></button>
	</div>
</div>
