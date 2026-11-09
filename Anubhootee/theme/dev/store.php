<?php
/**
 * Local test only: a working WooCommerce store with dummy products.
 * Runs after WooCommerce is installed and content.php has run.
 *
 * Payments: Cash on delivery only (no real money). On the live site, add Razorpay instead.
 */
require_once '/wordpress/wp-load.php';
wp_set_current_user( 1 );

if ( ! class_exists( 'WooCommerce' ) ) {
	echo 'ANU_STORE: WooCommerce not active';
	return;
}

// ---------- Settings ----------
foreach ( array(
	'woocommerce_store_address'               => 'FC Road',
	'woocommerce_store_city'                  => 'Pune',
	'woocommerce_store_postcode'              => '411004',
	'woocommerce_default_country'             => 'IN:MH',
	'woocommerce_allowed_countries'           => 'specific',
	'woocommerce_specific_allowed_countries'  => array( 'IN' ),
	'woocommerce_ship_to_countries'           => '',
	'woocommerce_currency'                    => 'INR',
	'woocommerce_currency_pos'                => 'left',
	'woocommerce_price_num_decimals'          => '0',
	'woocommerce_price_thousand_sep'          => ',',
	'woocommerce_calc_taxes'                  => 'no',
	'woocommerce_enable_coupons'              => 'yes',
	'woocommerce_enable_guest_checkout'       => 'yes',
	'woocommerce_enable_checkout_login_reminder' => 'yes',
	'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
	'woocommerce_enable_myaccount_registration' => 'yes',
	'woocommerce_manage_stock'                => 'yes',
	'woocommerce_enable_reviews'              => 'yes',
	'woocommerce_review_rating_verification_required' => 'no',
	'woocommerce_weight_unit'                 => 'g',
	'woocommerce_dimension_unit'              => 'cm',
	'woocommerce_coming_soon'                 => 'no',
	'woocommerce_store_pages_only'            => 'no',
	'woocommerce_task_list_hidden'            => 'yes',
	'woocommerce_task_list_complete'          => 'yes',
	'woocommerce_show_marketplace_suggestions' => 'no',
	'woocommerce_allow_tracking'              => 'no',
	'woocommerce_onboarding_profile'          => array( 'skipped' => true ),
	'woocommerce_downloads_require_login'     => 'no',
	'woocommerce_downloads_grant_access_after_payment' => 'yes',
	'woocommerce_file_download_method'        => 'redirect',
) as $k => $v ) {
	update_option( $k, $v );
}
delete_transient( '_wc_activation_redirect' );

// Shop, Cart, Checkout, My account pages.
WC_Install::create_pages();

// ---------- Payments: Cash on delivery (test only) ----------
update_option(
	'woocommerce_cod_settings',
	array(
		'enabled'            => 'yes',
		'title'              => 'Cash / UPI on delivery',
		'description'        => 'Pay with cash or UPI when your order arrives. (Test store: no real payment is taken.)',
		'instructions'       => 'Keep cash or your UPI app ready when the courier arrives.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	)
);

// ---------- Shipping: India, ₹60 flat, free above ₹999 ----------
$anu_has_zone = false;
foreach ( WC_Shipping_Zones::get_zones() as $z ) {
	if ( 'India' === $z['zone_name'] ) {
		$anu_has_zone = true;
	}
}
if ( ! $anu_has_zone ) {
	$zone = new WC_Shipping_Zone();
	$zone->set_zone_name( 'India' );
	$zone->add_location( 'IN', 'country' );
	$zone->save();
	$flat = $zone->add_shipping_method( 'flat_rate' );
	update_option( "woocommerce_flat_rate_{$flat}_settings", array( 'title' => 'Standard delivery (3–6 days)', 'tax_status' => 'none', 'cost' => '60' ) );
	$free = $zone->add_shipping_method( 'free_shipping' );
	update_option( "woocommerce_free_shipping_{$free}_settings", array( 'title' => 'Free delivery (orders over ₹999)', 'requires' => 'min_amount', 'min_amount' => '999', 'ignore_discounts' => 'no' ) );
}

// ---------- Product categories ----------
function anu_dev_img_id( $file ) {
	$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'title' => $file, 'numberposts' => 1, 'fields' => 'ids' ) );
	return $q ? (int) $q[0] : 0;
}
$img = array();
foreach ( array( 'road', 'food', 'utsav', 'ghat', 'kala', 'film' ) as $n ) {
	$img[ $n ] = anu_dev_img_id( $n . '.jpg' );
}

$cats = array(
	'merch'    => array( 'Merch', 'T-shirts, totes and mugs with the Anubhootee road line.', 'road' ),
	'spices'   => array( 'Food & Spices', 'Masalas and pantry staples from the families in our food stories.', 'food' ),
	'crafts'   => array( 'Handicrafts', 'Made by hand by the artisans in our stories. A share of every sale goes to the maker.', 'kala' ),
	'digital'  => array( 'Digital', 'E-books, route guides, recipe books and photo presets. Instant download.', 'film' ),
);
$cat_ids = array();
foreach ( $cats as $slug => $c ) {
	$t = term_exists( $c[0], 'product_cat' );
	if ( ! $t ) {
		$t = wp_insert_term( $c[0], 'product_cat', array( 'slug' => $slug, 'description' => $c[1] ) );
	}
	$cat_ids[ $slug ] = (int) $t['term_id'];
	update_term_meta( $cat_ids[ $slug ], 'thumbnail_id', $img[ $c[2] ] );
}
// Hide WooCommerce's default "Uncategorized" from the shop by giving it no products.

// ---------- Downloadable file for digital products ----------
$up  = wp_upload_dir();
$dir = $up['basedir'] . '/anu-digital';
wp_mkdir_p( $dir );
file_put_contents( $dir . '/sample-download.txt', "Anubhootee test download.\nOn the live site, upload the real PDF or ZIP in the product's Downloadable files section.\n" );
$file_url = $up['baseurl'] . '/anu-digital/sample-download.txt';

// ---------- Products ----------
// [ name, category, regular price, sale price, image, short description, description, sku, stock, weight(g), featured ]
$products = array(
	array( 'Road Line T-shirt', 'merch', 0, 0, 'road', 'Heavyweight cotton tee with the Anubhootee road line across the chest.', 'Printed in Tiruppur on 220 GSM combed cotton. Relaxed fit. Machine wash cold, inside out.', 'ANU-TEE', 0, 220, true ),
	array( 'Anubhootee Tote Bag', 'merch', 549, 0, 'road', 'A sturdy canvas tote for the market, the beach and the road.', 'Natural 12 oz canvas, inside pocket, screen-printed by hand. 38 × 42 cm.', 'ANU-TOTE', 40, 180, false ),
	array( 'Ghat Roads Enamel Mug', 'merch', 449, 399, 'ghat', 'The tapri mug, in enamel. Chips gracefully, like all good mugs.', 'Enamel-coated steel, 350 ml, with the ghat road illustration. Not microwave safe.', 'ANU-MUG', 25, 260, false ),
	array( 'अनुभूती Cap', 'merch', 499, 0, 'road', 'Six-panel cotton cap with अनुभूती embroidered in turmeric thread.', 'Adjustable strap, one size.', 'ANU-CAP', 30, 90, false ),
	array( 'Kolhapuri Kanda Lasun Masala (200 g)', 'spices', 249, 219, 'food', 'The red, fiery onion-garlic masala behind every Kolhapuri misal and rassa.', 'Hand-pounded by a family in Kolhapur from Byadgi and Sankeshwari chillies, onion, garlic, coconut and 18 spices. Store in an airtight jar.', 'ANU-KLM', 60, 220, true ),
	array( 'Goda Masala (200 g)', 'spices', 229, 0, 'food', 'The sweet, smoky Maharashtrian masala for amti, masale bhaat and usal.', 'Roasted slowly over a wood fire with dagad phool, coconut and sesame. Made in small batches in Satara.', 'ANU-GODA', 50, 220, false ),
	array( 'Malvani Masala (200 g)', 'spices', 239, 0, 'food', 'For Malvani fish curry, kombdi vade and kaju usal.', 'From a home kitchen in Malvan. 22 spices, sun-dried and stone-ground.', 'ANU-MALV', 45, 220, false ),
	array( 'Kokum Agal (500 ml)', 'spices', 199, 0, 'food', 'Kokum extract for solkadhi and sherbet. Just add water, or coconut milk.', 'From Konkan kokum farms. Contains salt. Refrigerate after opening.', 'ANU-KOKUM', 35, 600, false ),
	array( 'Warli Painting on Canvas', 'crafts', 2499, 0, 'kala', 'Original Warli painting of the tarpa dance, hand-painted on canvas.', 'Painted by an artist in Dahanu with white acrylic on red-earth canvas. 45 × 60 cm, unframed and rolled in a tube. Each piece is unique; yours will differ slightly from the photo.', 'ANU-WARLI', 6, 400, true ),
	array( 'Handmade Kolhapuri Chappals', 'crafts', 1299, 0, 'kala', 'Vegetable-tanned leather, hand-stitched in Kolhapur.', 'Made to order in 7–10 days by a third-generation maker. Choose your usual size; the leather softens with wear.', 'ANU-KOLH', 12, 450, false ),
	array( 'Paithani-motif Cushion Cover', 'crafts', 899, 749, 'kala', 'Silk-blend cushion cover with the Paithani peacock motif.', 'Woven in Yeola. 40 × 40 cm, zip closure. Dry clean only.', 'ANU-CUSH', 15, 150, false ),
	array( 'Western Ghats Monsoon Road Guide (e-book)', 'digital', 299, 0, 'ghat', '12 monsoon routes through the Sahyadris, with maps, chai stops and safety tips.', 'PDF, 64 pages. Delivered instantly by email and in your account.', 'ANU-EB-GHAT', 0, 0, true ),
	array( 'Maharashtrian Street Food Recipe Book', 'digital', 249, 0, 'food', '30 street-food recipes, from vada pav to misal, tested at home.', 'PDF, 80 pages, with photos. Delivered instantly.', 'ANU-EB-FOOD', 0, 0, false ),
	array( 'Anubhootee Lightroom Presets', 'digital', 399, 0, 'film', '10 presets for warm, film-like travel photos: the look of our stories.', 'For Lightroom Classic and Lightroom mobile. ZIP download with install guide.', 'ANU-PRESETS', 0, 0, false ),
);

$ids = array();
foreach ( $products as $p ) {
	list( $name, $cat, $reg, $sale, $image, $short, $long, $sku, $stock, $weight, $featured ) = $p;
	if ( wc_get_product_id_by_sku( $sku ) ) {
		$ids[ $sku ] = wc_get_product_id_by_sku( $sku );
		continue;
	}

	if ( 'ANU-TEE' === $sku ) {
		$product = new WC_Product_Variable();
	} else {
		$product = new WC_Product_Simple();
	}
	$product->set_name( $name );
	$product->set_status( 'publish' );
	$product->set_sku( $sku );
	$product->set_short_description( $short );
	$product->set_description( $long );
	$product->set_category_ids( array( $cat_ids[ $cat ] ) );
	$product->set_image_id( $img[ $image ] );
	$product->set_featured( $featured );
	$product->set_reviews_allowed( true );

	if ( 'digital' === $cat ) {
		$product->set_virtual( true );
		$product->set_downloadable( true );
		$dl = new WC_Product_Download();
		$dl->set_id( wp_generate_uuid4() );
		$dl->set_name( $name . ' (sample file)' );
		$dl->set_file( $file_url );
		$product->set_downloads( array( $dl ) );
		$product->set_download_limit( -1 );
		$product->set_download_expiry( -1 );
	} elseif ( $weight ) {
		$product->set_weight( $weight );
	}

	if ( $product instanceof WC_Product_Simple ) {
		$product->set_regular_price( (string) $reg );
		if ( $sale ) {
			$product->set_sale_price( (string) $sale );
		}
		if ( $stock ) {
			$product->set_manage_stock( true );
			$product->set_stock_quantity( $stock );
		}
	} else {
		$attr = new WC_Product_Attribute();
		$attr->set_name( 'Size' );
		$attr->set_options( array( 'S', 'M', 'L', 'XL' ) );
		$attr->set_visible( true );
		$attr->set_variation( true );
		$product->set_attributes( array( $attr ) );
	}
	$pid = $product->save();

	if ( $product instanceof WC_Product_Variable ) {
		foreach ( array( 'S' => 799, 'M' => 799, 'L' => 799, 'XL' => 849 ) as $size => $price ) {
			$v = new WC_Product_Variation();
			$v->set_parent_id( $pid );
			$v->set_attributes( array( 'size' => $size ) );
			$v->set_regular_price( (string) $price );
			$v->set_sku( 'ANU-TEE-' . $size );
			$v->set_manage_stock( true );
			$v->set_stock_quantity( 'XL' === $size ? 3 : 20 );
			$v->set_weight( $weight );
			$v->save();
		}
		WC_Product_Variable::sync( $pid );
	}
	$ids[ $sku ] = $pid;
}

// Related items for upsells.
foreach ( array( 'ANU-KLM' => array( 'ANU-GODA', 'ANU-MALV' ), 'ANU-WARLI' => array( 'ANU-CUSH' ), 'ANU-EB-GHAT' => array( 'ANU-PRESETS' ) ) as $sku => $up_skus ) {
	$pr = wc_get_product( $ids[ $sku ] );
	$pr->set_upsell_ids( array_map( function ( $s ) use ( $ids ) { return $ids[ $s ]; }, $up_skus ) );
	$pr->save();
}

// ---------- Reviews ----------
$reviews = array(
	array( 'ANU-KLM', 'Priya K.', 5, 'Tastes exactly like the misal in Kolhapur. Use half of what you think!' ),
	array( 'ANU-KLM', 'Rahul D.', 4, 'Very hot, very good. Packaging could be a zip-lock.' ),
	array( 'ANU-WARLI', 'Sneha P.', 5, 'Even more beautiful in person. It came rolled safely in a tube.' ),
	array( 'ANU-TEE', 'Amit S.', 5, 'Thick cotton, great fit. The road line is a nice touch.' ),
	array( 'ANU-EB-GHAT', 'Neha J.', 5, 'Used it for a Malshej trip. The chai-stop list alone is worth it.' ),
	array( 'ANU-GODA', 'Vaishali M.', 5, 'Smells like my aaji\'s kitchen.' ),
);
foreach ( $reviews as $r ) {
	$pid = $ids[ $r[0] ];
	if ( get_comments( array( 'post_id' => $pid, 'author_email' => sanitize_title( $r[1] ) . '@example.com', 'count' => true ) ) ) {
		continue;
	}
	$cid = wp_insert_comment(
		array(
			'comment_post_ID'      => $pid,
			'comment_author'       => $r[1],
			'comment_author_email' => sanitize_title( $r[1] ) . '@example.com',
			'comment_content'      => $r[3],
			'comment_type'         => 'review',
			'comment_approved'     => 1,
		)
	);
	update_comment_meta( $cid, 'rating', $r[2] );
	update_comment_meta( $cid, 'verified', 1 );
}
foreach ( array_unique( wp_list_pluck( array_map( function ( $r ) use ( $ids ) { return array( 'id' => $ids[ $r[0] ] ); }, $reviews ), 'id' ) ) as $pid ) {
	WC_Comments::clear_transients( $pid );
}

// ---------- Coupon ----------
if ( ! wc_get_coupon_id_by_code( 'ANUBHOOTEE10' ) ) {
	$coupon = new WC_Coupon();
	$coupon->set_code( 'ANUBHOOTEE10' );
	$coupon->set_description( 'Test coupon: 10% off.' );
	$coupon->set_discount_type( 'percent' );
	$coupon->set_amount( 10 );
	$coupon->save();
}

// ---------- Homepage: Kala chapter's button goes to the shop ----------
$kala = get_term_by( 'name', 'Art & Handicrafts', 'category' );
if ( $kala ) {
	update_term_meta( $kala->term_id, 'anu_link', get_permalink( wc_get_page_id( 'shop' ) ) );
}

echo 'ANU_STORE: ' . count( $ids ) . ' products, shop page ' . wc_get_page_id( 'shop' );
