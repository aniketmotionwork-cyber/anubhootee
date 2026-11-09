<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Placeholder content for local testing. All text is dummy copy; the photos are
 * Wikimedia Commons (CC BY-SA 4.0, see database/seeders/images/CREDITS.md). Replace before launch.
 */
class DemoContentSeeder extends Seeder
{
    private function photo(string $file, string $dir): string
    {
        $path = $dir.'/'.$file;
        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, file_get_contents(database_path('seeders/images/'.$file)));
        }

        return $path;
    }

    private static function p(string ...$paras): string
    {
        return implode('', array_map(fn ($t) => '<p>'.$t.'</p>', $paras));
    }

    private static function h(string $t): string
    {
        return '<h2>'.$t.'</h2>';
    }

    private static function q(string $t, string $cite): string
    {
        return '<blockquote><p>'.$t.'</p><p><small>— '.$cite.'</small></p></blockquote>';
    }

    public function run(): void
    {
        $author = User::first();

        // ---------- Sections / homepage chapters ----------
        $sections = [
            'road' => ['road-travel', 'Road Travel', 'प्रवास', 'Rasta', 'रस्ता', 'Explore routes', 'मार्ग पहा', 'road.jpg', 'Mariappan Ramachandran · CC BY-SA 4.0',
                'Routes, ghats, highways and backroads, with the chai stops and the tips that only come from driving them.', 'घाट, महामार्ग आणि आडवाटा — चहाच्या टपऱ्या आणि अनुभवातून आलेल्या टिप्ससह.'],
            'food' => ['food', 'Food', 'खाद्य', 'Chav', 'चव', 'Taste stories', 'चवीच्या गोष्टी', 'food.jpg', 'Ankushkr2020 · CC BY-SA 4.0',
                'Street food, home kitchens and regional thalis, and the people who cook them.', 'रस्त्यावरचे पदार्थ, घरची स्वयंपाकघरे आणि प्रादेशिक थाळ्या.'],
            'utsav' => ['culture', 'Culture', 'संस्कृती', 'Utsav', 'उत्सव', 'Feel the culture', 'संस्कृती अनुभवा', 'utsav.jpg', 'Achary K Krishnan · CC BY-SA 4.0',
                'Festivals, pilgrimages, music and the traditions that hold a town together.', 'सण, वारी, संगीत आणि गावाला एकत्र बांधणाऱ्या परंपरा.'],
            'goshti' => ['stories', 'Stories', 'गोष्टी', 'Goshti', 'गोष्टी', 'Read stories', 'गोष्टी वाचा', 'ghat.jpg', 'Rangan Datta Wiki · CC BY-SA 4.0',
                'Long stories and short diary entries from the road.', 'रस्त्यावरच्या मोठ्या गोष्टी आणि छोट्या डायरी नोंदी.'],
            'kala' => ['art', 'Art', 'कला', 'Kala', 'कला', 'Meet the makers', 'कारागिरांना भेटा', 'kala.jpg', 'PratishKhedekar · CC BY-SA 4.0',
                'Warli, Paithani, Kolhapuri leather and the makers behind them.', 'वारली, पैठणी, कोल्हापुरी चामडे आणि त्यामागचे हात.'],
            'film' => ['films', 'Films', 'चित्रपट', 'Chitrapat', 'चित्रपट', 'Watch films', 'चित्रपट पहा', 'film.jpg', 'Clément Bardot · CC BY-SA 4.0',
                'Vlogs, reels and long-form documentaries.', 'व्लॉग्स, रील्स आणि माहितीपट.'],
        ];
        $cat = [];
        $order = 1;
        foreach ($sections as $key => $s) {
            $cat[$key] = Category::updateOrCreate(['slug' => $s[0]], [
                'name' => $s[1], 'name_mr' => $s[2], 'big_title' => $s[3], 'deva_word' => $s[4],
                'cta' => $s[5], 'cta_mr' => $s[6], 'cover' => $this->photo($s[7], 'chapters'), 'cover_credit' => $s[8],
                'description' => $s[9], 'description_mr' => $s[10],
                'show_on_home' => true, 'home_order' => $order++,
            ]);
        }

        $img = fn (string $f) => $this->photo($f, 'posts');
        $credit = [
            'road.jpg' => 'Mariappan Ramachandran · CC BY-SA 4.0', 'food.jpg' => 'Ankushkr2020 · CC BY-SA 4.0',
            'utsav.jpg' => 'Achary K Krishnan · CC BY-SA 4.0', 'ghat.jpg' => 'Rangan Datta Wiki · CC BY-SA 4.0',
            'kala.jpg' => 'PratishKhedekar · CC BY-SA 4.0', 'film.jpg' => 'Clément Bardot · CC BY-SA 4.0',
        ];

        // ---------- Posts ----------
        // [type, section, title, subtitle, location, photo, tags, body, facts, tips, extra]
        $posts = [
            ['story', 'road', 'Chasing the monsoon through the Western Ghats', 'पावसाळ्यातला घाटमाथा', 'Western Ghats', 'road.jpg', ['Monsoon', 'WesternGhats', 'RoadTrip'],
                self::p('The rain arrived at Lonavala like a curtain coming down. By the time we reached the ghat, the road had become a river of headlights moving through the cloud.')
                .self::h('The first stop').self::p('A tapri at the top of the ghat, a kettle that never stops, and a hundred bikers in raincoats sharing one tin roof.')
                .self::q('घाटात पाऊस नसतो, घाटच पाऊस होतो.', 'A chai-stall owner near Malshej')
                .self::h('What we ate').self::p('Kanda bhaji, cutting chai and roasted corn with lime and chilli. Nothing has ever tasted better.'),
                [['label' => 'Distance', 'value' => '186 km'], ['label' => 'Drive time', 'value' => '6 hrs'], ['label' => 'Best season', 'value' => 'Jul – Sep'], ['label' => 'Chai stops', 'value' => '11']],
                [['title' => 'Start early.', 'text' => 'Fog thickens after 3 pm; plan to be off the ghat sections before dark.'], ['title' => 'Check landslide alerts.', 'text' => 'Follow local police and PWD updates during heavy rain weeks.'], ['title' => 'Carry cash.', 'text' => 'Many tapris can\'t take UPI when the network drops.']],
                ['title_mr' => 'पावसाळ्यात पश्चिम घाटातून', 'summary_mr' => 'लोणावळ्यापासून माळशेजपर्यंत पावसाचा पाठलाग.', 'body_mr' => self::p('लोणावळ्यात पाऊस पडद्यासारखा कोसळला. घाटात पोहोचेपर्यंत रस्ता ढगांतून सरकणाऱ्या दिव्यांची नदी झाला होता.').self::h('पहिला थांबा').self::p('घाटमाथ्यावरची टपरी, कधीही न थांबणारी किटली आणि एका पत्र्याखाली उभे शंभर बायकर्स.'), 'is_featured' => true]],
            ['story', 'road', 'Mumbai to Goa on the coastal highway', 'कोकण किनाऱ्याचा रस्ता', 'Konkan coast', 'road.jpg', ['Konkan', 'RoadTrip', 'Coast'],
                self::p('The coastal road doesn\'t hurry. It bends around every creek, climbs every headland, and drops you at a beach you didn\'t know was there.')
                .self::h('Day one: Alibag to Dapoli').self::p('We left before sunrise to catch the first ferry at Dighi. The jetty smelled of diesel and dried fish.')
                .self::q('रस्ता लांब आहे, पण घाई कसली?', 'A ferry operator at Bagmandla')
                .self::h('Day two: Ratnagiri to Vengurla').self::p('Mango orchards, red laterite walls and a fish thali at every stop.'),
                [['label' => 'Distance', 'value' => '590 km'], ['label' => 'Drive time', 'value' => '2 days'], ['label' => 'Best season', 'value' => 'Oct – Feb'], ['label' => 'Ferries', 'value' => '3']],
                [['title' => 'Check ferry timings.', 'text' => 'They change with the tide and the season.'], ['title' => 'Keep cash.', 'text' => 'Many beach shacks and ferries don\'t take cards.']], []],
            ['story', 'road', 'Malshej Ghat in the first rain', 'माळशेज घाटातला पहिला पाऊस', 'Malshej Ghat', 'road.jpg', ['Monsoon', 'Ghats', 'Waterfalls'],
                self::p('The first rain turns Malshej into a hundred waterfalls.').self::h('Why go early').self::p('Weekends bring traffic jams on the ghat. Leave by 5 am and you\'ll have the viewpoints to yourself.'),
                [['label' => 'Distance', 'value' => '130 km'], ['label' => 'From', 'value' => 'Mumbai'], ['label' => 'Best season', 'value' => 'Jun – Sep']],
                [['title' => 'Watch for rockfall.', 'text' => 'Don\'t stop under overhanging cliffs.'], ['title' => 'Carry a rain jacket.', 'text' => 'An umbrella is useless in the ghat wind.']], []],
            ['story', 'road', 'Ten things to pack for an Indian road trip', 'रोड ट्रिपची यादी', 'Anywhere', 'road.jpg', ['Tips', 'Packing'],
                self::p('After eight years of road trips, this is the list that lives in our glovebox.'), [],
                [['title' => 'A paper map.', 'text' => 'Phones lose signal exactly where you need them.'], ['title' => 'Cash in small notes.', 'text' => 'For tolls, tapris and parking.'], ['title' => 'A steel bottle.', 'text' => 'Refill it at dhabas and temples.'], ['title' => 'A tyre inflator.', 'text' => 'Cheap, and it can save a day.'], ['title' => 'Snacks from home.', 'text' => 'Chivda, laddoos and thepla travel well.'], ['title' => 'A first-aid kit.', 'text' => 'Include ORS and motion-sickness tablets.'], ['title' => 'Patience.', 'text' => 'Every road in India has its own timetable.']], []],

            ['story', 'food', "Why Kolhapur's misal burns brighter", 'कोल्हापूरची मिसळ इतकी झणझणीत का?', 'Kolhapur', 'food.jpg', ['MisalPav', 'StreetFood', 'Kolhapur'],
                self::p('In Kolhapur, the misal arrives in two bowls: one with the sprouts and farsan, and one with the kat, a red, fiery gravy you pour as you dare.')
                .self::h('The kat is everything').self::p('Every misal house guards its kanda-lasun masala. The colour comes from Byadgi chillies; the heat from Sankeshwari.')
                .self::q('मिसळ खायची असेल तर घाम यायलाच हवा.', 'A regular at a misal house in Kolhapur'),
                [['label' => 'Price', 'value' => '₹80–₹120'], ['label' => 'Best time', 'value' => '7–10 am'], ['label' => 'Spice', 'value' => '🌶🌶🌶']], [],
                ['title_mr' => 'कोल्हापूरची मिसळ इतकी झणझणीत का?', 'body_mr' => self::p('कोल्हापुरात मिसळ दोन वाट्यांत येते: एकात मटकी आणि फरसाण, दुसऱ्यात लालभडक कट — हिंमत असेल तितका ओता.')]],
            ['story', 'food', 'The perfect vada pav, according to six stalls', 'वडापावचा शोध', 'Mumbai', 'food.jpg', ['StreetFood', 'VadaPav', 'Mumbai'],
                self::p('Every Mumbaikar has a favourite vada pav, and every one of them is right. We ate at six stalls in one day to find out why.')
                .self::h('The chutney decides everything').self::p('Dry garlic chutney, green chutney, sweet tamarind: the balance changes from stall to stall.')
                .self::q('पाव ताजा हवा, बाकी सगळं हाताचं काम.', 'A vendor outside Dadar station'), [], [], []],
            ['story', 'food', 'Puran poli in a Satara kitchen', 'साताऱ्यातली पुरणपोळी', 'Satara', 'food.jpg', ['Recipe', 'Festive', 'HomeCooking'],
                self::p('Aaji has made puran poli every Holi for sixty years. She measures nothing and gets it right every time.')
                .self::h('The puran').self::p('Chana dal, jaggery, cardamom and nutmeg, cooked until a spoon stands up in it.'),
                [['label' => 'Makes', 'value' => '12 polis'], ['label' => 'Time', 'value' => '3 hrs'], ['label' => 'Difficulty', 'value' => 'Medium'], ['label' => 'Serve with', 'value' => 'Ghee']], [], []],
            ['story', 'food', 'Solkadhi and the Malvani thali', 'मालवणी थाळी', 'Malvan', 'food.jpg', ['Malvani', 'Seafood', 'Konkan'],
                self::p('Pink, cool and sharp with kokum: solkadhi is how a Malvani meal ends, and sometimes how it begins.')
                .self::h('What\'s on the thali').self::p('Bangda fry, kombdi vade, a coconut curry, rice bhakri and a glass of solkadhi.'), [],
                [['title' => 'Go at lunch.', 'text' => 'The catch is freshest.']], []],

            ['story', 'utsav', 'A morning of colour in the old city', 'रंगांची सकाळ', 'Pune', 'utsav.jpg', ['Holi', 'Festivals'],
                self::p('By 8 am the lanes of the old city were already pink, green and gold.').self::q('बुरा न मानो, होली है!', 'Every child with a pichkari'), [], [], []],
            ['story', 'utsav', 'Ganeshotsav in Pune: ten days, ten moods', 'पुण्याचा गणेशोत्सव', 'Pune', 'utsav.jpg', ['Ganeshotsav', 'Festivals', 'Pune'],
                self::p('For ten days the city belongs to Bappa. The dhol-tasha pathaks practise for months, and the whole city can hear it.')
                .self::h('The manache Ganpati').self::p('Kasba, Tambdi Jogeshwari, Guruji Talim, Tulshibaug and Kesariwada lead the procession, in that order, as they have for over a century.')
                .self::q('गणपती बाप्पा मोरया, पुढच्या वर्षी लवकर या!', 'The whole city, on the last day'), [],
                [['title' => 'Walk, don\'t drive.', 'text' => 'Central Pune closes to traffic on visarjan day.'], ['title' => 'Protect your ears.', 'text' => 'The dhol pathaks are loud. That\'s the point.']], []],
            ['story', 'utsav', 'Wari: walking with the pilgrims to Pandharpur', 'वारी', 'Alandi to Pandharpur', 'utsav.jpg', ['Wari', 'Pilgrimage'],
                self::p('Every year, lakhs of warkaris walk 250 km to Pandharpur, singing abhangs the whole way. We walked three days with them.')
                .self::q('माऊली, माऊली.', 'The greeting every warkari uses'),
                [['label' => 'Distance', 'value' => '250 km'], ['label' => 'Duration', 'value' => '21 days'], ['label' => 'Month', 'value' => 'Ashadh']], [], []],
            ['story', 'utsav', 'Lavani: the stage, the dholki, the dancers', 'लावणी', 'Narayangaon', 'utsav.jpg', ['Lavani', 'Music', 'Folk'],
                self::p('In a tent theatre in Narayangaon, the dholki starts at ten at night and doesn\'t stop until two.')
                .self::h('An art with a long memory').self::p('Lavani carries centuries of Marathi poetry, satire and love songs.'), [], [], []],

            ['story', 'goshti', 'Evening aarti on the ghats of Varanasi', 'घाटावरची संध्याकाळ', 'Varanasi', 'ghat.jpg', ['Varanasi', 'Diary'],
                self::p('The bells start before the lamps are lit. By the time the aarti begins, the river has disappeared under a thousand small flames.'), [], [], []],
            ['diary', 'goshti', 'Diary: a puncture outside Nashik', 'डायरी: नाशिकजवळ पंक्चर', 'Nashik', 'road.jpg', ['Diary', 'RoadTrip'],
                self::p('Day 4. Rear tyre gave up at 6 pm, 20 km short of Nashik. A boy on a cycle appeared out of nowhere, pointed at a shed, and disappeared again.', 'The puncture-wala charged us ₹80 and gave us tea for free.'), [], [], []],
            ['diary', 'goshti', 'Diary: tea at 5 am in Mahabaleshwar', 'डायरी: पहाटेचा चहा', 'Mahabaleshwar', 'road.jpg', ['Diary', 'Hills'],
                self::p('Woke up to fog so thick the hotel garden vanished. The tea stall at Arthur\'s Seat was already open.').self::q('धुकं गेलं की दरी दिसेल. थांबा थोडं.', 'The tea-stall owner'), [], [], []],
            ['diary', 'goshti', 'Diary: the last bus to Alibag', 'डायरी: अलिबागची शेवटची एसटी', 'Alibag', 'road.jpg', ['Diary', 'STBus'],
                self::p('Missed the ferry, so we took the red ST bus. Three hours, every village, one goat, and the best conversation of the trip.'), [], [], []],

            ['story', 'kala', 'Warli: painting the everyday', 'वारली चित्रकला', 'Dahanu', 'kala.jpg', ['Warli', 'FolkArt', 'Artisans'],
                self::p('Circles, triangles and lines: from these shapes Warli artists paint the harvest, the wedding and the tarpa dance.')
                .self::h('Rice paste on red earth').self::p('Traditionally, Warli was painted on mud walls with a bamboo stick and white rice paste.'), [], [], []],
            ['story', 'kala', 'Meet the Kolhapuri chappal makers', 'कोल्हापुरी चपलेचे कारागीर', 'Kolhapur', 'kala.jpg', ['Kolhapuri', 'Leather'],
                self::p('A pair of real Kolhapuris takes two days and no machines. We spent a week in the lanes where they\'re made.')
                .self::q('चप्पल पायाला नाही, माणसाला बसायला हवी.', 'A third-generation chappal maker'), [], [], []],
            ['story', 'kala', 'Paithani: a sari in two years', 'पैठणी', 'Yeola', 'kala.jpg', ['Paithani', 'Weaving'],
                self::p('The peacock on a Paithani pallu can take a weaver months. A full bridal sari can take two years.'), [], [], []],

            ['documentary', 'film', 'The Salt Roads of Konkan', 'माहितीपट: कोकणातील मिठाचे रस्ते', 'Konkan', 'film.jpg', ['Documentary', 'Konkan', 'Salt'],
                self::p('A 38-minute film about the salt pans of the Konkan coast, and the families who have farmed salt for generations.')
                .self::h('Synopsis').self::p('From January to May, the salt pans fill, dry and shine. We followed one family through a season.')
                .self::h('Credits').self::p('Directed by the Anubhootee team. Music: local artists from Uran.'),
                [['label' => 'Runtime', 'value' => '38 min'], ['label' => 'Language', 'value' => 'Marathi'], ['label' => 'Subtitles', 'value' => 'English'], ['label' => 'Year', 'value' => '2026']], [], ['is_featured' => true]],
            ['vlog', 'film', '24 hours in Kolhapur', 'व्लॉग: कोल्हापुरात २४ तास', 'Kolhapur', 'food.jpg', ['Vlog', 'Kolhapur'],
                self::p('Misal for breakfast, tambda-pandhra rassa for lunch, and a walk around Rankala lake at sunset.'), [], [], []],
            ['vlog', 'film', 'Monsoon ride to Bhandardara', 'व्लॉग: भंडारदरा', 'Bhandardara', 'road.jpg', ['Vlog', 'Monsoon'],
                self::p('Umbrella Falls, Arthur Lake and a night under fireflies.'), [], [], []],
            ['documentary', 'film', 'Hands of Paithan', 'माहितीपट: पैठणचे हात', 'Paithan', 'kala.jpg', ['Documentary', 'Paithani'],
                self::p('A short film about three generations of Paithani weavers.'),
                [['label' => 'Runtime', 'value' => '22 min'], ['label' => 'Language', 'value' => 'Marathi'], ['label' => 'Year', 'value' => '2026']], [], []],
        ];

        $n = count($posts);
        foreach ($posts as $i => [$type, $section, $title, $subtitle, $location, $photo, $tags, $body, $facts, $tips, $extra]) {
            Post::updateOrCreate(['slug' => \Illuminate\Support\Str::slug($title)], array_merge([
                'type' => $type,
                'category_id' => $cat[$section]->id,
                'author_id' => $author?->id,
                'title' => $title,
                'subtitle' => $subtitle,
                'summary' => strip_tags(explode('</p>', $body)[0]),
                'body' => $body,
                'location' => $location,
                'cover' => $img($photo),
                'cover_credit' => $credit[$photo],
                'tags' => $tags,
                'facts' => $facts ?: null,
                'tips' => $tips ?: null,
                'published_at' => now()->subDays(($n - $i) * 3)->setTime(9, 0),
            ], $extra));
        }

        // ---------- Pages ----------
        $note = '<p><em>Placeholder text for the test site. Replace it before launch.</em></p>';
        $pages = [
            ['about', 'About', 'आमच्याबद्दल', true, false, 1,
                self::p('Anubhootee (अनुभूती) means “experience”: the kind you feel, not just see.', 'We are a small team from Maharashtra who travel India by road, eat everything, and film and write about the people we meet. This site is where those stories live, in English and in Marathi.')
                .self::h('What you\'ll find here').self::p('Road-trip guides with honest tips, food stories from street stalls and home kitchens, festivals and folk art, diaries from the road, and our vlogs and documentaries.'),
                self::p('अनुभूती म्हणजे फक्त पाहिलेला नाही, तर मनापासून जाणवलेला अनुभव.', 'आम्ही महाराष्ट्रातली एक छोटी टीम आहोत. रस्त्याने भारत फिरतो, सगळं खातो, आणि भेटलेल्या माणसांबद्दल लिहितो आणि चित्रित करतो.')],
            ['contact', 'Contact', 'संपर्क', false, true, 2,
                self::p('Have a story idea, a collaboration, or just want to say hello? Write to us using the form below, or email '.config('anubhootee.email').'.', 'We reply within two working days.'),
                self::p('गोष्टीची कल्पना, सहकार्य, किंवा फक्त नमस्कार? खालील फॉर्ममधून लिहा.')],
            ['privacy-policy', 'Privacy Policy', 'गोपनीयता धोरण', false, true, 3,
                self::p('We collect only what we need to run the site: the details you send through the contact form, your email if you subscribe, and basic anonymous analytics.')
                .self::h('Your choices').self::p('You can ask us to see, correct or delete your data at any time by writing to '.config('anubhootee.email').'.').$note, null],
            ['terms', 'Terms of Use', 'वापराच्या अटी', false, true, 4,
                self::p('All stories, photos and videos on this site belong to Anubhootee unless credited otherwise. Please ask before reusing them.').$note, null],
        ];
        foreach ($pages as [$slug, $title, $titleMr, $inMenu, $inFooter, $sort, $body, $bodyMr]) {
            Page::updateOrCreate(['slug' => $slug], [
                'title' => $title, 'title_mr' => $titleMr, 'body' => $body, 'body_mr' => $bodyMr,
                'in_menu' => $inMenu, 'in_footer' => $inFooter, 'sort' => $sort, 'is_published' => true,
            ]);
        }
    }
}
