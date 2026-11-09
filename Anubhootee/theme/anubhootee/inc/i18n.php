<?php
/**
 * Tiny English/Marathi string helper.
 *
 * Polylang (or WordPress itself) sets the locale to "mr" on Marathi pages.
 * The theme's own UI labels are switched here so no .mo compilation is needed.
 *
 * @package Anubhootee
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is the current request Marathi?
 *
 * @return bool
 */
function anu_is_marathi() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( $lang ) {
			return 'mr' === $lang;
		}
	}
	return 0 === strpos( determine_locale(), 'mr' );
}

/**
 * Translate a theme UI label.
 *
 * @param string $key Label key.
 * @return string
 */
function anu_t( $key ) {
	static $strings = array(
		'next_story'  => array( 'Next story', 'पुढची गोष्ट' ),
		'min_read'    => array( '%d min read', '%d मिनिटे वाचन' ),
		'copy_link'   => array( 'Copy link', 'लिंक कॉपी' ),
		'copied'      => array( 'Copied', 'कॉपी झाली' ),
		'photo'       => array( 'Photo', 'छायाचित्र' ),
		'drag'        => array( 'Drag', 'ओढा' ),
		'explore'     => array( 'Explore', 'पहा' ),
		'chapters'    => array( 'Chapters', 'अध्याय' ),
		'rights'      => array( 'All rights reserved.', 'सर्व हक्क राखीव.' ),
		'by'          => array( 'By', 'लेखक' ),
	);
	if ( ! isset( $strings[ $key ] ) ) {
		return $key;
	}
	return $strings[ $key ][ anu_is_marathi() ? 1 : 0 ];
}

/**
 * Convert Latin digits to Devanagari digits (for Marathi dates/numbers).
 *
 * @param string $text Text.
 * @return string
 */
function anu_deva_digits( $text ) {
	if ( ! anu_is_marathi() ) {
		return $text;
	}
	return strtr( $text, array( '0' => '०', '1' => '१', '2' => '२', '3' => '३', '4' => '४', '5' => '५', '6' => '६', '7' => '७', '8' => '८', '9' => '९' ) );
}
