<?php
/**
 * Story hero: full-screen featured photo, category · location · reading time, title, subtitle (excerpt).
 *
 * @package Anubhootee
 */

$anu_id = anu_block_post_id( isset( $block ) ? $block : null );
if ( ! $anu_id ) {
	return;
}
$anu_thumb    = get_post_thumbnail_id( $anu_id );
$anu_cats     = get_the_category( $anu_id );
$anu_cat      = $anu_cats ? $anu_cats[0] : null;
$anu_location = get_post_meta( $anu_id, 'anu_location', true );
$anu_minutes  = anu_deva_digits( sprintf( anu_t( 'min_read' ), anu_reading_time( $anu_id ) ) );
$anu_meta     = array_filter( array( $anu_location, $anu_minutes ) );
$anu_sub      = has_excerpt( $anu_id ) ? get_the_excerpt( $anu_id ) : '';
$anu_credit   = $anu_thumb ? wp_get_attachment_caption( $anu_thumb ) : '';
$anu_classes  = 'anu-hero alignfull' . ( $anu_thumb ? '' : ' no-image' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => $anu_classes ) ); // phpcs:ignore ?>>
	<?php
	if ( $anu_thumb ) {
		echo wp_get_attachment_image(
			$anu_thumb,
			'full',
			false,
			array(
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'sizes'         => '100vw',
			)
		);
	}
	?>
	<div class="anu-hero__inner">
		<div class="anu-kicker">
			<?php if ( $anu_cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $anu_cat ) ); ?>"><?php echo esc_html( $anu_cat->name ); ?></a>
				<span class="anu-kicker__bar" aria-hidden="true"></span>
			<?php endif; ?>
			<span><?php echo esc_html( implode( ' · ', $anu_meta ) ); ?></span>
		</div>
		<h1 class="anu-hero__title"><?php echo esc_html( get_the_title( $anu_id ) ); ?></h1>
		<?php if ( $anu_sub ) : ?>
			<p class="anu-hero__sub"><?php echo esc_html( $anu_sub ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( $anu_credit ) : ?>
		<p class="anu-hero__credit"><?php echo esc_html( anu_t( 'photo' ) . ': ' . $anu_credit ); ?></p>
	<?php endif; ?>
</section>
