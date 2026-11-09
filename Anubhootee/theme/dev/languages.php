<?php
/**
 * Local test only: set up Polylang with English (default) + मराठी, and add one
 * Marathi translation of the sample story so the EN · मराठी switch can be tried.
 * Runs after setup.php and after Polylang is installed (see playground.html).
 */
require_once '/wordpress/wp-load.php';
wp_set_current_user( 1 );

if ( ! class_exists( 'PLL_Admin_Model' ) ) {
	echo 'ANU_LANG: Polylang not active';
	return;
}

$anu_options = class_exists( 'WP_Syntex\Polylang\Options\Options' ) ? new WP_Syntex\Polylang\Options\Options() : get_option( 'polylang' );
$anu_model   = new PLL_Admin_Model( $anu_options );

$anu_have = wp_list_pluck( $anu_model->get_languages_list(), 'slug' );
foreach ( array(
	array( 'name' => 'English', 'slug' => 'en', 'locale' => 'en_US', 'rtl' => false, 'term_group' => 0, 'flag' => 'gb' ),
	array( 'name' => 'मराठी', 'slug' => 'mr', 'locale' => 'mr', 'rtl' => false, 'term_group' => 1, 'flag' => 'in' ),
) as $anu_lang ) {
	if ( ! in_array( $anu_lang['slug'], $anu_have, true ) ) {
		$anu_model->add_language( $anu_lang );
	}
}
$anu_model->set_language_in_mass(); // Existing posts and categories become English.

// Marathi version of the sample story.
$anu_en = get_page_by_path( 'chasing-the-monsoon-through-the-western-ghats', OBJECT, 'post' );
if ( $anu_en && ! get_page_by_path( 'pavsalyat-ghat', OBJECT, 'post' ) ) {
	$anu_mr = wp_insert_post(
		array(
			'post_title'    => 'पावसाळ्यात पश्चिम घाटातून',
			'post_name'     => 'pavsalyat-ghat',
			'post_excerpt'  => 'पावसाळ्यातला घाटमाथा',
			'post_content'  => "<!-- wp:paragraph -->\n<p>ढग खाली उतरले होते आणि घाटाचा रस्ता धुक्यात हरवला होता. (चाचणीसाठी नमुना मजकूर.)</p>\n<!-- /wp:paragraph -->",
			'post_status'   => 'publish',
			'post_category' => wp_get_post_categories( $anu_en->ID ),
		)
	);
	// pll_* helpers aren't loaded in the run where languages were just added, so use the model.
	$anu_model->post->set_language( $anu_mr, 'mr' );
	$anu_model->post->save_translations( $anu_en->ID, array( 'en' => $anu_en->ID, 'mr' => $anu_mr ) );
	set_post_thumbnail( $anu_mr, get_post_thumbnail_id( $anu_en->ID ) );
}

// Polylang's /mr/ URL rules aren't hooked in yet during this run, so drop the
// stored rules and let WordPress rebuild them on the next page load.
delete_option( 'rewrite_rules' );
echo 'ANU_LANG: ' . implode( ',', wp_list_pluck( $anu_model->get_languages_list(), 'slug' ) );
