<?php
/**
 * Copyright line.
 *
 * @package Anubhootee
 */
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-copyright' ) ); // phpcs:ignore ?>>&copy; <?php echo esc_html( anu_deva_digits( wp_date( 'Y' ) ) . ' ' . get_bloginfo( 'name' ) ); ?></p>
