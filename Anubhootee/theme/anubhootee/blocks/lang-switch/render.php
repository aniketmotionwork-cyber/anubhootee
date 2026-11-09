<?php
/**
 * Language switch (Polylang). Renders nothing until Polylang has 2+ languages.
 *
 * @package Anubhootee
 */

if ( ! function_exists( 'pll_the_languages' ) ) {
	return;
}
$anu_langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0 ) );
if ( empty( $anu_langs ) || count( $anu_langs ) < 2 ) {
	return;
}
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-lang' ) ); // phpcs:ignore ?> aria-label="<?php esc_attr_e( 'Language', 'anubhootee' ); ?>">
	<?php
	foreach ( $anu_langs as $anu_lang ) {
		$anu_label = 'mr' === $anu_lang['slug'] ? 'मराठी' : strtoupper( $anu_lang['slug'] );
		printf(
			'<a href="%1$s" lang="%2$s" hreflang="%2$s" class="%3$s"%4$s>%5$s</a>',
			esc_url( $anu_lang['url'] ),
			esc_attr( $anu_lang['slug'] ),
			$anu_lang['current_lang'] ? 'is-current' : '',
			$anu_lang['current_lang'] ? ' aria-current="true"' : '',
			esc_html( $anu_label )
		);
	}
	?>
</nav>
