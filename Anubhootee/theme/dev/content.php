<?php
/**
 * Local test only: dummy stories, diaries, vlogs, documentaries and pages.
 * Runs after setup.php (which creates the six chapter categories and photos).
 * All text is placeholder copy, written to show how the site looks with real-ish content.
 */
require_once '/wordpress/wp-load.php';
wp_set_current_user( 1 );

/** Attachment ID of a photo setup.php uploaded, by file name (road.jpg, food.jpg …). */
function anu_dev_img( $file ) {
	$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'title' => $file, 'numberposts' => 1, 'fields' => 'ids' ) );
	return $q ? (int) $q[0] : 0;
}
function anu_dev_cat( $name ) {
	$t = get_term_by( 'name', $name, 'category' );
	return $t ? (int) $t->term_id : 0;
}
function anu_p( $text ) {
	return "<!-- wp:paragraph -->\n<p>$text</p>\n<!-- /wp:paragraph -->\n\n";
}
function anu_h( $text ) {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$text</h2>\n<!-- /wp:heading -->\n\n";
}
function anu_quote( $text, $cite ) {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>$text</p>\n<!-- /wp:paragraph --><cite>$cite</cite></blockquote>\n<!-- /wp:quote -->\n\n";
}
function anu_facts( $facts ) {
	$out = "<!-- wp:group {\"className\":\"anu-route\",\"layout\":{\"type\":\"grid\",\"columnCount\":4,\"minimumColumnWidth\":null}} -->\n<div class=\"wp-block-group anu-route\">";
	foreach ( $facts as $label => $value ) {
		$out .= "<!-- wp:group {\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group\"><!-- wp:paragraph {\"className\":\"anu-route-label\"} -->\n<p class=\"anu-route-label\">$label</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"className\":\"anu-route-value\"} -->\n<p class=\"anu-route-value\">$value</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
	}
	return $out . "</div>\n<!-- /wp:group -->\n\n";
}
function anu_tips( $title, $tips ) {
	$out = "<!-- wp:group {\"className\":\"anu-tips\",\"layout\":{\"type\":\"default\"}} -->\n<div class=\"wp-block-group anu-tips\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">$title</h3>\n<!-- /wp:heading -->\n\n<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">";
	foreach ( $tips as $strong => $rest ) {
		$out .= "<!-- wp:list-item -->\n<li><strong>$strong</strong> $rest</li>\n<!-- /wp:list-item -->\n\n";
	}
	return $out . "</ol>\n<!-- /wp:list --></div>\n<!-- /wp:group -->\n\n";
}
function anu_image( $id, $caption ) {
	$url = wp_get_attachment_image_url( $id, 'large' );
	return "<!-- wp:image {\"id\":$id,\"sizeSlug\":\"large\",\"align\":\"wide\"} -->\n<figure class=\"wp-block-image alignwide size-large\"><img src=\"$url\" alt=\"\" class=\"wp-image-$id\"/><figcaption class=\"wp-element-caption\">$caption</figcaption></figure>\n<!-- /wp:image -->\n\n";
}
function anu_video_slot( $what ) {
	return "<!-- wp:group {\"className\":\"anu-video-slot\",\"style\":{\"spacing\":{\"padding\":{\"top\":\"48px\",\"bottom\":\"48px\",\"left\":\"24px\",\"right\":\"24px\"}},\"color\":{\"background\":\"#14110E\",\"text\":\"#FAF6EF\"}},\"layout\":{\"type\":\"constrained\"}} -->\n<div class=\"wp-block-group anu-video-slot has-text-color has-background\" style=\"color:#FAF6EF;background-color:#14110E;padding-top:48px;padding-right:24px;padding-bottom:48px;padding-left:24px\"><!-- wp:paragraph {\"align\":\"center\"} -->\n<p class=\"has-text-align-center\">▶ $what</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph {\"align\":\"center\",\"fontSize\":\"small\"} -->\n<p class=\"has-text-align-center has-small-font-size\">Placeholder: paste your YouTube or Instagram link on its own line here and WordPress embeds the video.</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
}

$img = array(
	'road'  => anu_dev_img( 'road.jpg' ),
	'food'  => anu_dev_img( 'food.jpg' ),
	'utsav' => anu_dev_img( 'utsav.jpg' ),
	'ghat'  => anu_dev_img( 'ghat.jpg' ),
	'kala'  => anu_dev_img( 'kala.jpg' ),
	'film'  => anu_dev_img( 'film.jpg' ),
);
$cat = array(
	'road'  => anu_dev_cat( 'Road Travel' ),
	'food'  => anu_dev_cat( 'Food' ),
	'utsav' => anu_dev_cat( 'Festivals & Culture' ),
	'goshti'=> anu_dev_cat( 'Stories & Diaries' ),
	'kala'  => anu_dev_cat( 'Art & Handicrafts' ),
	'film'  => anu_dev_cat( 'Documentaries & Vlogs' ),
);

// Category descriptions (shown under the archive title).
$desc = array(
	'road'   => 'Routes, ghats, highways and backroads, with the chai stops and the tips that only come from driving them.',
	'food'   => 'Street food, home kitchens and regional thalis, and the people who cook them.',
	'utsav'  => 'Festivals, pilgrimages, music and the traditions that hold a town together.',
	'goshti' => 'Long stories and short diary entries from the road.',
	'kala'   => 'Warli, Paithani, Kolhapuri leather and the makers behind them. Some of it is in the shop.',
	'film'   => 'Vlogs, reels and long-form documentaries.',
);
foreach ( $desc as $k => $d ) {
	if ( $cat[ $k ] ) {
		wp_update_term( $cat[ $k ], 'category', array( 'description' => $d ) );
	}
}

// Give the existing four sample posts different photos per category (setup.php used one each).
// New posts: [ title, Marathi excerpt, location, cat, image, tags, body ].
$posts = array(
	array(
		'Mumbai to Goa on the coastal highway', 'कोकण किनाऱ्याचा रस्ता', 'Konkan coast', 'road', 'road', array( 'Konkan', 'RoadTrip', 'Coast' ),
		anu_p( 'The coastal road doesn\'t hurry. It bends around every creek, climbs every headland, and drops you at a beach you didn\'t know was there.' )
		. anu_facts( array( 'Distance' => '590 km', 'Drive time' => '2 days', 'Best season' => 'Oct – Feb', 'Ferries' => '3' ) )
		. anu_h( 'Day one: Alibag to Dapoli' ) . anu_p( 'We left before sunrise to catch the first ferry at Dighi. The jetty smelled of diesel and dried fish, and the boatman had a transistor tuned to old Marathi songs.' )
		. anu_quote( '"रस्ता लांब आहे, पण घाई कसली?"', 'A ferry operator at Bagmandla' )
		. anu_h( 'Day two: Ratnagiri to Vengurla' ) . anu_p( 'Mango orchards, red laterite walls and a fish thali at every stop. The road narrows after Ratnagiri, so plan to drive in daylight.' )
		. anu_tips( 'Important tips for this route', array( 'Check ferry timings.' => 'They change with the tide and the season.', 'Keep cash.' => 'Many beach shacks and ferries don\'t take cards.', 'Avoid the monsoon.' => 'Some coastal stretches flood between June and September.' ) ),
	),
	array(
		'Malshej Ghat in the first rain', 'माळशेज घाटातला पहिला पाऊस', 'Malshej Ghat', 'road', 'ghat', array( 'Monsoon', 'Ghats', 'Waterfalls' ),
		anu_p( 'The first rain turns Malshej into a hundred waterfalls. By noon the road is a river of headlights moving through the cloud.' )
		. anu_facts( array( 'Distance' => '130 km', 'From' => 'Mumbai', 'Best season' => 'Jun – Sep', 'Waterfalls' => 'Countless' ) )
		. anu_h( 'Why go early' ) . anu_p( 'Weekends bring traffic jams on the ghat. Leave by 5 am, and you\'ll have the viewpoints to yourself.' )
		. anu_image( $img['ghat'], 'The ghat road in the monsoon.' )
		. anu_tips( 'Before you go', array( 'Watch for rockfall.' => 'Don\'t stop under overhanging cliffs.', 'Carry a rain jacket.' => 'An umbrella is useless in the ghat wind.' ) ),
	),
	array(
		'Ten things to pack for an Indian road trip', 'रोड ट्रिपची यादी', 'Anywhere', 'road', 'road', array( 'Tips', 'RoadTrip', 'Packing' ),
		anu_p( 'After eight years of road trips, this is the list that lives in our glovebox.' )
		. anu_tips( 'The list', array( 'A paper map.' => 'Phones lose signal exactly where you need them.', 'Cash in small notes.' => 'For tolls, tapris and parking.', 'A steel bottle.' => 'Refill it at dhabas and temples.', 'A tyre inflator.' => 'Cheap, and it can save a day.', 'A torch.' => 'Power cuts are common in small towns.', 'Snacks from home.' => 'Chivda, laddoos and thepla travel well.', 'A first-aid kit.' => 'Include ORS and motion-sickness tablets.', 'A phone mount.' => 'So the navigator can rest.', 'A power bank.' => 'Fully charged, every night.', 'Patience.' => 'Every road in India has its own timetable.' ) ),
	),
	array(
		'The perfect vada pav, according to six stalls', 'वडापावचा शोध', 'Mumbai', 'food', 'food', array( 'StreetFood', 'VadaPav', 'Mumbai' ),
		anu_p( 'Every Mumbaikar has a favourite vada pav, and every one of them is right. We ate at six stalls in one day to find out why.' )
		. anu_h( 'The chutney decides everything' ) . anu_p( 'Dry garlic chutney, green chutney, sweet tamarind: the balance changes from stall to stall, and so does the city\'s loyalty.' )
		. anu_quote( '"पाव ताजा हवा, बाकी सगळं हाताचं काम."', 'A vendor outside Dadar station' )
		. anu_h( 'Our scorecard' ) . anu_p( 'We judged on crunch, spice, chutney and the fried green chilli on the side. The winner surprised us.' ),
	),
	array(
		'Puran poli in a Satara kitchen', 'साताऱ्यातली पुरणपोळी', 'Satara', 'food', 'food', array( 'Recipe', 'Festive', 'HomeCooking' ),
		anu_p( 'Aaji has made puran poli every Holi for sixty years. She measures nothing and gets it right every time.' )
		. anu_facts( array( 'Makes' => '12 polis', 'Time' => '3 hrs', 'Difficulty' => 'Medium', 'Serve with' => 'Ghee' ) )
		. anu_h( 'The puran' ) . anu_p( 'Chana dal, jaggery, cardamom and nutmeg, cooked until a spoon stands up in it.' )
		. anu_h( 'The rolling' ) . anu_p( 'The dough has to be softer than you think. Aaji rolls each one thin enough to read a newspaper through.' ),
	),
	array(
		'Solkadhi and the Malvani thali', 'मालवणी थाळी', 'Malvan', 'food', 'food', array( 'Malvani', 'Seafood', 'Konkan' ),
		anu_p( 'Pink, cool and sharp with kokum: solkadhi is how a Malvani meal ends, and sometimes how it begins.' )
		. anu_h( 'What\'s on the thali' ) . anu_p( 'Bangda fry, kombdi vade, a coconut curry, rice bhakri and a glass of solkadhi. Vegetarian thalis swap the fish for kaju usal.' )
		. anu_tips( 'Eating in Malvan', array( 'Go at lunch.' => 'The catch is freshest.', 'Ask for the masala.' => 'Many homes will sell you a packet to take back.' ) ),
	),
	array(
		'Ganeshotsav in Pune: ten days, ten moods', 'पुण्याचा गणेशोत्सव', 'Pune', 'utsav', 'utsav', array( 'Ganeshotsav', 'Festivals', 'Pune' ),
		anu_p( 'For ten days the city belongs to Bappa. The dhol-tasha pathaks practise for months, and the whole city can hear it.' )
		. anu_h( 'The manache Ganpati' ) . anu_p( 'Kasba, Tambdi Jogeshwari, Guruji Talim, Tulshibaug and Kesariwada lead the procession, in that order, as they have for over a century.' )
		. anu_quote( '"गणपती बाप्पा मोरया, पुढच्या वर्षी लवकर या!"', 'The whole city, on the last day' )
		. anu_tips( 'If you\'re visiting', array( 'Walk, don\'t drive.' => 'Central Pune closes to traffic on visarjan day.', 'Protect your ears.' => 'The dhol pathaks are loud. That\'s the point.' ) ),
	),
	array(
		'Wari: walking with the pilgrims to Pandharpur', 'वारी', 'Alandi to Pandharpur', 'utsav', 'utsav', array( 'Wari', 'Pilgrimage', 'Vitthal' ),
		anu_p( 'Every year, lakhs of warkaris walk 250 km to Pandharpur, singing abhangs the whole way. We walked three days with them.' )
		. anu_facts( array( 'Distance' => '250 km', 'Duration' => '21 days', 'Month' => 'Ashadh', 'Walkers' => 'Lakhs' ) )
		. anu_h( 'The rhythm of the dindi' ) . anu_p( 'Each group walks behind its own flag and its own singers. The rhythm of the taal sets your pace, and after a few hours you stop noticing your feet.' )
		. anu_quote( '"माऊली, माऊली."', 'The greeting every warkari uses' ),
	),
	array(
		'Lavani: the stage, the dholki, the dancers', 'लावणी', 'Narayangaon', 'utsav', 'utsav', array( 'Lavani', 'Music', 'Folk' ),
		anu_p( 'In a tent theatre in Narayangaon, the dholki starts at ten at night and doesn\'t stop until two.' )
		. anu_h( 'An art with a long memory' ) . anu_p( 'Lavani carries centuries of Marathi poetry, satire and love songs. The dancers we met learned from their mothers and grandmothers.' ),
	),
	array(
		'Diary: a puncture outside Nashik', 'डायरी: नाशिकजवळ पंक्चर', 'Nashik', 'goshti', 'road', array( 'Diary', 'RoadTrip' ),
		anu_p( 'Day 4. Rear tyre gave up at 6 pm, 20 km short of Nashik. A boy on a cycle appeared out of nowhere, pointed at a shed, and disappeared again.' )
		. anu_p( 'The puncture-wala charged us ₹80 and gave us tea for free. We\'ve paid more for worse evenings.' ),
	),
	array(
		'Diary: tea at 5 am in Mahabaleshwar', 'डायरी: पहाटेचा चहा', 'Mahabaleshwar', 'goshti', 'ghat', array( 'Diary', 'Hills' ),
		anu_p( 'Woke up to fog so thick the hotel garden vanished. The tea stall at Arthur\'s Seat was already open, the owner wrapped in a shawl, the kettle singing.' )
		. anu_quote( '"धुकं गेलं की दरी दिसेल. थांबा थोडं."', 'The tea-stall owner, refilling our glasses' ),
	),
	array(
		'Diary: the last bus to Alibag', 'डायरी: अलिबागची शेवटची एसटी', 'Alibag', 'goshti', 'road', array( 'Diary', 'ST Bus' ),
		anu_p( 'Missed the ferry, so we took the red ST bus. Three hours, every village, one goat, and the best conversation of the trip.' ),
	),
	array(
		'Warli: painting the everyday', 'वारली चित्रकला', 'Dahanu', 'kala', 'kala', array( 'Warli', 'FolkArt', 'Artisans' ),
		anu_p( 'Circles, triangles and lines: from these shapes Warli artists paint the harvest, the wedding and the tarpa dance.' )
		. anu_h( 'Rice paste on red earth' ) . anu_p( 'Traditionally, Warli was painted on mud walls with a bamboo stick and white rice paste. Today the same hands paint on canvas and cloth, and some of those pieces are in our shop.' )
		. anu_image( $img['kala'], 'A Warli artist at work.' ),
	),
	array(
		'Meet the Kolhapuri chappal makers', 'कोल्हापुरी चपलेचे कारागीर', 'Kolhapur', 'kala', 'kala', array( 'Kolhapuri', 'Leather', 'Artisans' ),
		anu_p( 'A pair of real Kolhapuris takes two days and no machines. We spent a week in the lanes where they\'re made.' )
		. anu_quote( '"चप्पल पायाला नाही, माणसाला बसायला हवी."', 'A third-generation chappal maker' ),
	),
	array(
		'Paithani: a sari in two years', 'पैठणी', 'Yeola', 'kala', 'kala', array( 'Paithani', 'Weaving', 'Textiles' ),
		anu_p( 'The peacock on a Paithani pallu can take a weaver months. A full bridal sari can take two years.' )
		. anu_h( 'The loom' ) . anu_p( 'Silk and real zari, woven by hand with a tapestry technique that makes both sides of the fabric look the same.' ),
	),
	array(
		'Documentary: The Salt Roads of Konkan', 'माहितीपट: कोकणातील मिठाचे रस्ते', 'Konkan', 'film', 'film', array( 'Documentary', 'Konkan', 'Salt' ),
		anu_p( 'A 38-minute film about the salt pans of the Konkan coast, and the families who have farmed salt for generations.' )
		. anu_video_slot( 'Watch the documentary' )
		. anu_facts( array( 'Runtime' => '38 min', 'Language' => 'Marathi', 'Subtitles' => 'English', 'Year' => '2026' ) )
		. anu_h( 'Synopsis' ) . anu_p( 'From January to May, the salt pans fill, dry and shine. We followed one family through a season.' )
		. anu_h( 'Credits' ) . anu_p( 'Directed by the Anubhootee team. Camera, sound and edit: Anubhootee. Music: local artists from Uran.' ),
	),
	array(
		'Vlog: 24 hours in Kolhapur', 'व्लॉग: कोल्हापुरात २४ तास', 'Kolhapur', 'film', 'food', array( 'Vlog', 'Kolhapur', 'Food' ),
		anu_p( 'Misal for breakfast, tambda-pandhra rassa for lunch, and a walk around Rankala lake at sunset.' )
		. anu_video_slot( 'Watch the vlog' )
		. anu_h( 'Places in this vlog' ) . anu_p( 'Mahalaxmi temple, Rankala lake, Bhausingji Road and the chappal lane.' ),
	),
	array(
		'Vlog: monsoon ride to Bhandardara', 'व्लॉग: भंडारदरा', 'Bhandardara', 'film', 'ghat', array( 'Vlog', 'Monsoon', 'Waterfalls' ),
		anu_p( 'Umbrella Falls, Arthur Lake and a night under fireflies.' )
		. anu_video_slot( 'Watch the vlog' ),
	),
	array(
		'Documentary: Hands of Paithan', 'माहितीपट: पैठणचे हात', 'Paithan', 'film', 'kala', array( 'Documentary', 'Paithani', 'Weaving' ),
		anu_p( 'A short film about three generations of Paithani weavers.' )
		. anu_video_slot( 'Watch the documentary' )
		. anu_facts( array( 'Runtime' => '22 min', 'Language' => 'Marathi', 'Subtitles' => 'English', 'Year' => '2026' ) ),
	),
);

$i = 0;
foreach ( $posts as $p ) {
	if ( get_page_by_path( sanitize_title( $p[0] ), OBJECT, 'post' ) ) {
		continue;
	}
	$pid = wp_insert_post(
		array(
			'post_title'    => $p[0],
			'post_excerpt'  => $p[1],
			'post_content'  => $p[6],
			'post_status'   => 'publish',
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - ( 40 - $i * 2 ) * DAY_IN_SECONDS ),
			'post_category' => array( $cat[ $p[3] ] ),
			'tags_input'    => $p[5],
		)
	);
	set_post_thumbnail( $pid, $img[ $p[4] ] );
	update_post_meta( $pid, 'anu_location', $p[2] );
	$i++;
}

// ---------- Pages ----------
function anu_dev_page( $slug, $title, $content ) {
	$existing = get_page_by_path( $slug );
	$data     = array( 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $title, 'post_content' => $content, 'post_status' => 'publish' );
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		return wp_update_post( $data );
	}
	return wp_insert_post( $data );
}

$note = anu_p( '<em>Placeholder text for the test site. Replace it with your own before launch; a lawyer or CA should review the policy pages.</em>' );

$about = anu_dev_page(
	'about',
	'About',
	anu_p( 'Anubhootee (अनुभूती) means "experience": the kind you feel, not just see.' )
	. anu_p( 'We are a small team from Maharashtra who travel India by road, eat everything, and film and write about the people we meet. This site is where those stories live, in English and in Marathi.' )
	. anu_image( $img['road'], 'On the road somewhere in the Sahyadris.' )
	. anu_h( 'What you\'ll find here' )
	. anu_p( 'Road-trip guides with honest tips, food stories from street stalls and home kitchens, festivals and folk art, diaries from the road, and our vlogs and documentaries.' )
	. anu_h( 'The shop' )
	. anu_p( 'We sell merch, spices from the families we meet, handicrafts made by the artisans in our stories, and digital guides. A share of every handicraft sale goes back to the maker.' )
);
anu_dev_page(
	'contact',
	'Contact',
	anu_p( 'Have a story idea, a collaboration, or a question about an order? Write to us.' )
	. anu_facts( array( 'Email' => 'hello@yourdomain.com', 'Instagram' => '@anubhootee', 'YouTube' => '/@anubhootee', 'Based in' => 'Pune' ) )
	. anu_p( 'We reply within two working days. For order questions, please include your order number.' )
	. $note
);
anu_dev_page(
	'shipping-policy',
	'Shipping Policy',
	anu_h( 'Where we ship' ) . anu_p( 'We ship across India. International shipping isn\'t available yet.' )
	. anu_h( 'Charges' ) . anu_p( 'Standard delivery is ₹60. Orders above ₹999 ship free. Digital products are delivered instantly by email and in your account.' )
	. anu_h( 'Delivery time' ) . anu_p( 'Orders are dispatched within 2 working days and usually arrive in 3–6 working days. Handicrafts made to order may take up to 10 days; the product page says so.' )
	. $note
);
anu_dev_page(
	'terms',
	'Terms & Conditions',
	anu_p( 'By using this website and buying from our shop you agree to these terms.' )
	. anu_h( 'Products and prices' ) . anu_p( 'Prices are in Indian rupees and include applicable taxes unless stated. Handmade products vary slightly from the photos; that\'s part of their charm.' )
	. anu_h( 'Content' ) . anu_p( 'All stories, photos and videos on this site belong to Anubhootee unless credited otherwise. Please ask before reusing them.' )
	. $note
);
// WooCommerce creates "Refund and Returns Policy" (slug refund_returns) as a draft; WordPress creates "Privacy Policy" as a draft.
anu_dev_page(
	'refund_returns',
	'Refund & Returns Policy',
	anu_h( 'Physical products' ) . anu_p( 'You can return unused merch and handicrafts within 7 days of delivery. Food and spices can\'t be returned once opened, for hygiene reasons.' )
	. anu_h( 'Damaged or wrong items' ) . anu_p( 'Send us a photo within 48 hours of delivery and we\'ll replace or refund it.' )
	. anu_h( 'Digital products' ) . anu_p( 'Because they are delivered instantly, digital products can\'t be refunded unless the file is faulty.' )
	. anu_h( 'Refund timing' ) . anu_p( 'Refunds go back to your original payment method within 5–7 working days of approval.' )
	. $note
);
anu_dev_page(
	'privacy-policy',
	'Privacy Policy',
	anu_p( 'We collect only what we need to run the site and deliver your orders: your name, address, email and phone number at checkout, and basic anonymous analytics.' )
	. anu_h( 'Payments' ) . anu_p( 'Payments are processed by our payment partner. We never see or store your card or UPI details.' )
	. anu_h( 'Your choices' ) . anu_p( 'You can ask us to see, correct or delete your data at any time by writing to hello@yourdomain.com.' )
	. $note
);
$sample = get_page_by_path( 'sample-page' );
if ( $sample ) {
	wp_delete_post( $sample->ID, true );
}
$privacy = get_page_by_path( 'privacy-policy' );
if ( $privacy ) {
	update_option( 'wp_page_for_privacy_policy', $privacy->ID );
}

echo 'ANU_CONTENT: ' . wp_count_posts()->publish . ' posts, ' . wp_count_posts( 'page' )->publish . ' pages';
