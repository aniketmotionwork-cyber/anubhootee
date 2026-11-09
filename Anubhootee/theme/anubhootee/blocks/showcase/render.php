<?php
/**
 * Chapter showcase: full-screen drag-through chapters (homepage).
 * Chapters come from categories marked "Show on homepage"; demo chapters are used until then.
 *
 * @package Anubhootee
 */

$anu_chapters = anu_get_chapters();
if ( empty( $anu_chapters ) ) {
	return;
}
$anu_data = array(
	'chapters' => array_values( $anu_chapters ),
	'photo'    => anu_t( 'photo' ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-showcase' ) ); // phpcs:ignore ?>>
	<div class="anu-sc-loader" aria-hidden="true"><div><div class="anu-sc-loader__word">अनुभूती</div><div class="anu-sc-loader__road"></div></div></div>

	<div class="anu-sc-stage">
		<canvas class="anu-sc-gl" aria-hidden="true"></canvas>
		<div class="anu-sc-fallback" aria-hidden="true"></div>
		<div class="anu-sc-shade" aria-hidden="true"></div>
	</div>

	<div class="anu-sc-slides" role="list">
		<?php foreach ( $anu_chapters as $anu_i => $anu_ch ) : ?>
			<div class="anu-sc-slide<?php echo 0 === $anu_i ? ' is-active' : ''; ?>" role="listitem">
				<div class="anu-sc-meta"><span class="anu-sc-num"><?php echo esc_html( anu_deva_digits( str_pad( (string) ( $anu_i + 1 ), 2, '0', STR_PAD_LEFT ) ) ); ?></span><span class="anu-sc-bar" aria-hidden="true"></span><span><?php echo esc_html( $anu_ch['tag'] ); ?></span></div>
				<h2 class="anu-sc-title"><?php echo esc_html( $anu_ch['title'] ); ?></h2>
				<?php if ( $anu_ch['deva'] ) : ?>
					<div class="anu-sc-deva" lang="mr"><?php echo esc_html( $anu_ch['deva'] ); ?></div>
				<?php endif; ?>
				<a class="anu-sc-more" href="<?php echo esc_url( $anu_ch['url'] ); ?>"><span class="anu-sc-line" aria-hidden="true"></span><?php echo esc_html( $anu_ch['cta'] ); ?></a>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="anu-sc-progress" aria-label="<?php echo esc_attr( anu_t( 'chapters' ) ); ?>">
		<?php foreach ( $anu_chapters as $anu_i => $anu_ch ) : ?>
			<button type="button" class="<?php echo 0 === $anu_i ? 'is-on' : ''; ?>" aria-label="<?php echo esc_attr( $anu_ch['tag'] ); ?>"><span></span></button>
		<?php endforeach; ?>
	</div>

	<div class="anu-sc-counter" aria-hidden="true"><b>01</b> / <?php echo esc_html( str_pad( (string) count( $anu_chapters ), 2, '0', STR_PAD_LEFT ) ); ?></div>
	<div class="anu-sc-credit"></div>
	<div class="anu-sc-hint" aria-hidden="true"><?php echo esc_html( anu_is_marathi() ? 'ओढा' : 'Drag · ओढा' ); ?></div>

	<script type="application/json" class="anu-sc-data"><?php echo wp_json_encode( $anu_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
</div>
