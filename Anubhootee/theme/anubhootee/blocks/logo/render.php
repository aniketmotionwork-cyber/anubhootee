<?php
/**
 * Logo: site name + road line.
 *
 * @package Anubhootee
 */

$anu_home = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
?>
<a <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-logo' ) ); // phpcs:ignore ?> href="<?php echo esc_url( $anu_home ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<span class="anu-logo__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
	<span class="anu-logo__road" aria-hidden="true"></span>
</a>
