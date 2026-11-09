<?php
/**
 * Local test content for WordPress Playground (not part of the theme zip).
 */
require_once '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

update_option( 'blogname', 'Anubhootee' );
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules( true );

$img_dir = get_template_directory() . '/assets/img/';
function anu_dev_attach( $file, $caption ) {
	$up  = wp_upload_bits( basename( $file ), null, file_get_contents( $file ) );
	$id  = wp_insert_attachment( array( 'post_mime_type' => 'image/jpeg', 'post_title' => basename( $file ), 'post_excerpt' => $caption, 'post_status' => 'inherit' ), $up['file'] );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $up['file'] ) );
	return $id;
}

$chapters = array(
	array( 'Road Travel', 'Rasta', 'रस्ता', 'Explore routes', 'road.jpg', 'Mariappan Ramachandran · CC BY-SA 4.0' ),
	array( 'Food', 'Chav', 'चव', 'Taste stories', 'food.jpg', 'Ankushkr2020 · CC BY-SA 4.0' ),
	array( 'Festivals & Culture', 'Utsav', 'उत्सव', 'Feel the culture', 'utsav.jpg', 'Achary K Krishnan · CC BY-SA 4.0' ),
	array( 'Stories & Diaries', 'Goshti', 'गोष्टी', 'Read stories', 'ghat.jpg', 'Rangan Datta Wiki · CC BY-SA 4.0' ),
	array( 'Art & Handicrafts', 'Kala', 'कला', 'Visit the shop', 'kala.jpg', 'PratishKhedekar · CC BY-SA 4.0' ),
	array( 'Documentaries & Vlogs', 'Chitrapat', 'चित्रपट', 'Watch films', 'film.jpg', 'Clément Bardot · CC BY-SA 4.0' ),
);
$cat_ids = array(); $img_ids = array();
foreach ( $chapters as $i => $c ) {
	$t   = wp_insert_term( $c[0], 'category' );
	$tid = is_wp_error( $t ) ? $t->get_error_data() : $t['term_id'];
	$aid = anu_dev_attach( $img_dir . $c[4], $c[5] );
	foreach ( array( 'anu_show' => 1, 'anu_order' => $i + 1, 'anu_title' => $c[1], 'anu_deva' => $c[2], 'anu_cta' => $c[3], 'anu_cover' => $aid, 'anu_credit' => $c[5] ) as $k => $v ) {
		update_term_meta( $tid, $k, $v );
	}
	$cat_ids[] = $tid; $img_ids[] = $aid;
}

$body = file_get_contents( get_template_directory() . '/patterns/story-starter.php' );
$body = trim( preg_replace( '/^<\?php.*?\?>/s', '', $body ) );

$posts = array(
	array( 'Chasing the monsoon through the Western Ghats', 'पावसाळ्यातला घाटमाथा', 'Western Ghats', 0, 0, array( 'Monsoon', 'WesternGhats', 'RoadTrip' ) ),
	array( "Why Kolhapur's misal burns brighter", 'कोल्हापूरची मिसळ इतकी झणझणीत का?', 'Kolhapur', 1, 1, array( 'MisalPav', 'StreetFood' ) ),
	array( 'A morning of colour in the old city', 'रंगांची सकाळ', 'Pune', 2, 2, array( 'Holi', 'Festivals' ) ),
	array( 'Evening aarti on the ghats of Varanasi', 'घाटावरची संध्याकाळ', 'Varanasi', 3, 3, array( 'Varanasi', 'Diary' ) ),
);
foreach ( $posts as $i => $p ) {
	$pid = wp_insert_post( array(
		'post_title'   => $p[0],
		'post_excerpt' => $p[1],
		'post_content' => $body,
		'post_status'  => 'publish',
		'post_date'    => gmdate( 'Y-m-d H:i:s', time() - ( 10 - $i ) * DAY_IN_SECONDS ),
		'post_category'=> array( $cat_ids[ $p[3] ] ),
		'tags_input'   => $p[5],
	) );
	set_post_thumbnail( $pid, $img_ids[ $p[4] ] );
	update_post_meta( $pid, 'anu_location', $p[2] );
}
wp_delete_post( 1, true ); // "Hello world"
update_option( 'show_on_front', 'posts' );
echo "Anubhootee test content ready\n";
