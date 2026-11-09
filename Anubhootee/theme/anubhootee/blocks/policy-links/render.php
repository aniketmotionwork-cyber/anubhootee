<?php
/**
 * Footer links to the store's policy pages. Razorpay asks for these to be visible.
 * Each link appears only when a published page with that slug exists.
 *
 * @package Anubhootee
 */

$anu_links = array(
	'contact'            => array( 'Contact', 'संपर्क' ),
	'shipping-policy'    => array( 'Shipping', 'शिपिंग' ),
	'refund_returns'     => array( 'Refunds', 'परतावा' ),
	'privacy-policy'     => array( 'Privacy', 'गोपनीयता' ),
	'terms'              => array( 'Terms', 'अटी' ),
);
$anu_mr    = anu_is_marathi();
$anu_items = array();
foreach ( $anu_links as $anu_slug => $anu_label ) {
	$anu_page = get_page_by_path( $anu_slug );
	if ( ! $anu_page || 'publish' !== $anu_page->post_status ) {
		continue;
	}
	$anu_id = $anu_page->ID;
	if ( $anu_mr && function_exists( 'pll_get_post' ) ) {
		$anu_tr = pll_get_post( $anu_id, 'mr' );
		$anu_id = $anu_tr ? $anu_tr : $anu_id;
	}
	$anu_items[] = sprintf( '<a href="%s">%s</a>', esc_url( get_permalink( $anu_id ) ), esc_html( $anu_label[ $anu_mr ? 1 : 0 ] ) );
}
if ( ! $anu_items ) {
	return;
}
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'anu-policy' ) ); // phpcs:ignore ?> aria-label="<?php esc_attr_e( 'Policies', 'anubhootee' ); ?>">
	<?php echo implode( '', $anu_items ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>
</nav>
