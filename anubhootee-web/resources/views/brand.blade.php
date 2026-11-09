@extends('layouts.site')
@php
    /*
     * Brand style guide: logo, colours, core fonts and the six display fonts.
     * Each display font: [css family, name, styles => css family, sample, use, case]
     */
    $displayFonts = [
        [
            'id' => 'raga', 'name' => 'Raga', 'kind' => 'Sports display',
            'styles' => ['Regular' => 'Raga'],
            'sample' => 'RASTA 2026', 'case' => 'upper',
            'use' => 'Numbers, chapter counters, road-trip posters and YouTube thumbnails. Short words only.',
            'pair' => 'Inter for body text',
        ],
        [
            'id' => 'contrue', 'name' => 'Contrue Fonte', 'kind' => 'Duo · Blackletter + Sans, each with a Blur style',
            'styles' => ['Blackletter' => 'Contrue Blackletter', 'Blackletter Blur' => 'Contrue Blackletter Blur', 'Sans' => 'Contrue Sans', 'Sans Blur' => 'Contrue Sans Blur'],
            'sample' => 'Chitrapat', 'case' => 'none',
            'use' => 'Documentary titles and film posters. The Blur styles suit title cards and motion.',
            'pair' => 'Contrue Sans for credits',
        ],
        [
            'id' => 'newyork', 'name' => 'New York', 'kind' => 'Duo · New + York',
            'styles' => ['New' => 'NY New', 'York' => 'NY York'],
            'sample' => 'Stories you can taste', 'case' => 'none',
            'use' => 'New (tall condensed capitals): poster headlines and Instagram story covers. York (signature script): sign-offs and “— Team Anubhootee” on thumbnails.',
            'pair' => 'Inter for body text',
        ],
        [
            'id' => 'duobles', 'name' => 'Duobles Glomaflow', 'kind' => 'Duo · Condensed + Handwritten',
            'styles' => ['Condensed' => 'Duobles Glomaflow', 'Handwritten' => 'Duobles Glomaflow Handwritten'],
            'sample' => 'Diary · Day 4, Nashik', 'case' => 'none',
            'use' => 'Condensed for tall thumbnail titles; Handwritten for diary entries, notes and map labels.',
            'pair' => 'Inter for captions',
        ],
        [
            'id' => 'bunga', 'name' => 'Bunga Mekar', 'kind' => 'Duo · Display + Script',
            'styles' => ['Display' => 'Bunga Mekar Display', 'Script' => 'Bunga Mekar Script'],
            'sample' => 'Utsav & Chav', 'case' => 'none',
            'use' => 'Festival and food features, recipe cards, greetings for Diwali, Holi and Ganeshotsav.',
            'pair' => 'Mukta for the Marathi line underneath',
        ],
        [
            'id' => 'ashanti', 'name' => 'Ashanti Spirit', 'kind' => 'Duo · Regular + Bold',
            'styles' => ['Regular' => 'Ashanti Spirit|400', 'Bold' => 'Ashanti Spirit|700'],
            'sample' => 'Kala · Folk Art', 'case' => 'none',
            'use' => 'Art & handicrafts: Warli, Paithani and artisan stories. Use for headlines, not paragraphs.',
            'pair' => 'Inter for body text',
        ],
    ];
    $colours = [
        ['Saffron', '#FF6B1A', 'Primary accent: buttons on hover, highlights'],
        ['Turmeric', '#F5B700', 'The road line, progress bars, badges'],
        ['Deep Indigo', '#1E1B4B', 'Secondary: night scenes, film pages'],
        ['Ink', '#14110E', 'Text, footer, dark sections'],
        ['Chalk', '#FAF6EF', 'Page background, text on photos'],
        ['Muted', '#8A8279', 'Labels, captions, metadata'],
    ];
    // Each display font on a photo, the way it would appear on a poster or thumbnail.
    // [font id, photo, kicker, headline + css, second line + css, Marathi line]
    $posters = [
        ['raga', 'road.jpg', 'Road Travel', ['RASTA', "font-family: 'Raga'; font-size: clamp(40px, 5vw, 78px); white-space: nowrap;"], ['12 ROUTES · 2026', "font-family: 'Raga'; font-size: 20px; letter-spacing: .06em;"], 'रस्ता'],
        ['contrue', 'film.jpg', 'Documentary', ['Chitrapat', "font-family: 'Contrue Blackletter'; font-size: clamp(54px, 6.4vw, 96px);"], ['A FILM BY ANUBHOOTEE', "font-family: 'Contrue Sans Blur'; font-size: 18px; letter-spacing: .12em;"], 'चित्रपट'],
        ['newyork', 'utsav.jpg', 'Festivals', ['HOLI IN PUNE', "font-family: 'NY New'; font-size: clamp(60px, 7.4vw, 112px); line-height: .9;"], ['Anubhootee', "font-family: 'NY York'; font-size: 44px; color: #F5B700;"], 'रंगांची सकाळ'],
        ['duobles', 'ghat.jpg', 'Diary', ['VARANASI', "font-family: 'Duobles Glomaflow'; font-size: clamp(58px, 7vw, 104px);"], ['evening aarti, day 4', "font-family: 'Duobles Glomaflow Handwritten'; font-size: 34px;"], 'घाटावरची संध्याकाळ'],
        ['bunga', 'food.jpg', 'Food', ['MISAL PAV', "font-family: 'Bunga Mekar Display'; font-size: clamp(54px, 6.4vw, 96px);"], ["Kolhapur's favourite", "font-family: 'Bunga Mekar Script'; font-size: 40px; color: #F5B700;"], 'चव'],
        ['ashanti', 'kala.jpg', 'Art & Handicrafts', ['Warli', "font-family: 'Ashanti Spirit'; font-weight: 400; font-size: clamp(60px, 7.4vw, 112px);"], ['PAINTING THE EVERYDAY', "font-family: 'Ashanti Spirit'; font-weight: 700; font-size: 20px; letter-spacing: .08em;"], 'कला'],
    ];
    $fontNames = collect($displayFonts)->pluck('name', 'id');
    $alphabet ='ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789 & ? ! ₹';
@endphp

@section('title', __('Brand'))
@section('description', 'Anubhootee brand guide: logo, colours and typefaces.')

@push('head')
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="{{ asset('fonts/brand/brand-fonts.css') }}">
    <style>
        .brand { --pad: max(16px, 3vw); }
        .brand-sec { padding: clamp(56px, 9vw, 120px) var(--pad); border-bottom: 1px solid var(--anu-line); }
        .brand-sec--dark { background: var(--anu-ink); color: var(--anu-chalk); border-color: rgba(250,246,239,.12); }
        .brand-label { font-size: 11px; font-weight: 500; letter-spacing: .24em; text-transform: uppercase; color: var(--anu-muted); margin: 0 0 18px; display: flex; gap: 12px; align-items: center; }
        .brand-label::before { content: ""; width: 28px; height: 1px; background: currentColor; }
        .brand-h { font-size: clamp(36px, 5vw, 64px); font-weight: 500; letter-spacing: -.04em; line-height: 1; margin: 0 0 16px; }
        .brand-lead { max-width: 620px; color: var(--anu-muted); font-size: 17px; margin: 0; }

        /* Hero */
        .brand-hero { min-height: 82vh; display: grid; align-content: end; background: var(--anu-ink); color: var(--anu-chalk); padding: 160px var(--pad) 56px; position: relative; overflow: hidden; }
        .brand-hero::before { content: ""; position: absolute; inset: 0 var(--pad); border-inline: 1px solid rgba(250,244,234,.07);
            background: repeating-linear-gradient(90deg, transparent 0, transparent calc(100%/12 - 1px), rgba(250,244,234,.07) calc(100%/12 - 1px), rgba(250,244,234,.07) calc(100%/12)); pointer-events: none; }
        .brand-mark { position: relative; justify-self: start; width: max-content; max-width: 100%; }
        .brand-mark__name { font-weight: 700; font-size: clamp(44px, 9vw, 150px); letter-spacing: .14em; line-height: 1; margin-right: -.14em; }
        .brand-mark__road { height: clamp(4px, .6vw, 8px); margin-top: clamp(12px, 1.6vw, 22px); background: repeating-linear-gradient(90deg, var(--anu-turmeric) 0 clamp(26px, 3.6vw, 52px), transparent 0 clamp(40px, 5.4vw, 78px)); animation: brand-drive 2.4s linear infinite; }
        @keyframes brand-drive { to { background-position: clamp(40px, 5.4vw, 78px) 0; } }
        .brand-mark__tag { font-family: var(--anu-mr); font-weight: 300; font-size: clamp(18px, 2.2vw, 30px); margin-top: 20px; opacity: .85; }
        .brand-hero__meta { position: relative; margin-top: 64px; display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap; font-size: 11px; letter-spacing: .22em; text-transform: uppercase; color: rgba(250,244,234,.55); border-top: 1px solid rgba(250,244,234,.16); padding-top: 16px; }

        /* Logo variants */
        .brand-logos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 40px; }
        .brand-logos > div { aspect-ratio: 16 / 10; display: grid; place-items: center; border: 1px solid var(--anu-line); }
        .brand-logos .anu-logo__name { font-size: clamp(14px, 1.6vw, 22px); }
        .brand-logos .on-ink { background: var(--anu-ink); color: var(--anu-chalk); border-color: var(--anu-ink); }
        .brand-logos .on-saffron { background: var(--anu-saffron); color: var(--anu-ink); border-color: var(--anu-saffron); }
        .brand-logos .on-saffron .anu-logo__road { background: repeating-linear-gradient(90deg, var(--anu-ink) 0 10px, transparent 10px 16px); }

        /* Colours */
        .brand-colours { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-top: 40px; }
        .brand-swatch { border: 1px solid var(--anu-line); background: #fff; }
        .brand-swatch__chip { aspect-ratio: 1; }
        .brand-swatch__info { padding: 14px; font-size: 13px; line-height: 1.45; }
        .brand-swatch__info b { display: block; font-size: 15px; font-weight: 500; }
        .brand-swatch__info button { font: inherit; font-size: 12px; letter-spacing: .08em; color: var(--anu-muted); background: none; border: 0; padding: 0; cursor: pointer; }
        .brand-swatch__info small { display: block; color: var(--anu-muted); margin-top: 6px; }

        /* Core fonts */
        .brand-core { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 40px; }
        .brand-core--4 { grid-template-columns: repeat(4, 1fr); }
        .brand-core > div { border-top: 1px solid var(--anu-ink); padding-top: 18px; }
        .brand-core .big { font-size: clamp(48px, 6vw, 88px); line-height: 1; margin: 8px 0 14px; }
        .brand-core p { margin: 0; color: var(--anu-ink-2); font-size: 15px; }

        /* Fonts on photos */
        .brand-posters { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 40px; }
        .brand-poster { position: relative; aspect-ratio: 4 / 5; overflow: hidden; background: var(--anu-ink); color: #fff; text-decoration: none; display: block; }
        .brand-poster img { position: absolute; inset: 0; width: 100%; height: 100%; max-width: none; object-fit: cover; transition: transform 1.2s cubic-bezier(.2,.7,.2,1); }
        .brand-poster:hover img { transform: scale(1.04); }
        .brand-poster::after { content: ""; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(11,9,7,.82) 0%, rgba(11,9,7,.25) 50%, rgba(11,9,7,.1) 100%); }
        .brand-poster__font { position: absolute; top: 16px; left: 16px; z-index: 2; font-size: 10px; font-weight: 600; letter-spacing: .2em; text-transform: uppercase; background: var(--anu-chalk); color: var(--anu-ink); padding: 6px 9px; }
        .brand-poster__body { position: absolute; left: 22px; right: 22px; bottom: 22px; z-index: 2; display: flex; flex-direction: column; gap: 6px; }
        .brand-poster__kicker { font-size: 10px; letter-spacing: .24em; text-transform: uppercase; opacity: .75; display: flex; gap: 10px; align-items: center; }
        .brand-poster__kicker::before { content: ""; width: 20px; height: 1px; background: #F5B700; }
        .brand-poster__head { line-height: .95; word-break: break-word; }
        .brand-poster__second { line-height: 1.1; opacity: .95; }
        .brand-poster__mr { font-family: var(--anu-mr); font-weight: 300; font-size: 18px; opacity: .8; margin-top: 4px; }
        @media (max-width: 900px) { .brand-posters { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 560px) { .brand-posters { grid-template-columns: 1fr; } }

        /* Tester */
        .brand-tester { position: sticky; top: 0; z-index: 40; background: rgba(250,246,239,.94); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); border-bottom: 1px solid var(--anu-line); padding: 14px var(--pad); display: flex; gap: 20px; align-items: center; flex-wrap: wrap; }
        .brand-tester input[type=text] { flex: 1 1 280px; font: inherit; font-size: 17px; padding: 10px 0; border: 0; border-bottom: 1px solid var(--anu-ink); background: transparent; outline: none; }
        .brand-tester label { font-size: 11px; letter-spacing: .2em; text-transform: uppercase; color: var(--anu-muted); display: flex; gap: 10px; align-items: center; }
        .brand-tester input[type=range] { accent-color: var(--anu-saffron); width: 140px; }
        .brand-tester button { font: inherit; font-size: 11px; letter-spacing: .2em; text-transform: uppercase; background: none; border: 1px solid var(--anu-line); padding: 9px 12px; cursor: pointer; }
        .brand-tester button.is-on { background: var(--anu-ink); color: var(--anu-chalk); border-color: var(--anu-ink); }

        /* Display font specimens */
        .brand-font { padding: clamp(48px, 7vw, 96px) var(--pad); border-bottom: 1px solid var(--anu-line); }
        .brand-font:nth-of-type(even) { background: #F3EDE3; }
        .brand-font.is-dark { background: var(--anu-ink); color: var(--anu-chalk); border-color: rgba(250,246,239,.12); }
        .brand-font__head { display: grid; grid-template-columns: auto 1fr auto; gap: 24px; align-items: baseline; border-top: 1px solid currentColor; padding-top: 16px; }
        .brand-font__num { font-size: 11px; letter-spacing: .24em; opacity: .6; }
        .brand-font__name { font-size: 22px; font-weight: 500; letter-spacing: -.01em; }
        .brand-font__name small { display: block; font-size: 12px; letter-spacing: .14em; text-transform: uppercase; opacity: .55; font-weight: 400; margin-top: 4px; }
        .brand-font__file { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; opacity: .5; }
        .brand-font__sample { margin: clamp(28px, 4vw, 48px) 0 8px; line-height: 1.02; word-break: break-word; outline: none; }
        .brand-font__styles { display: grid; gap: 22px; margin-top: 28px; }
        .brand-font__style { display: grid; grid-template-columns: 160px 1fr; gap: 24px; align-items: baseline; border-top: 1px solid rgba(128,120,110,.25); padding-top: 14px; }
        .brand-font__style span { font-size: 11px; letter-spacing: .2em; text-transform: uppercase; opacity: .6; }
        .brand-font__style div { font-size: clamp(24px, 3vw, 40px); line-height: 1.15; word-break: break-word; }
        .brand-font__use { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 36px; font-size: 15px; }
        .brand-font__use b { display: block; font-size: 11px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; opacity: .55; margin-bottom: 6px; }
        .brand-note { font-size: 14px; color: var(--anu-muted); max-width: 720px; }

        @media (max-width: 900px) {
            .brand-colours { grid-template-columns: repeat(3, 1fr); }
            .brand-core, .brand-logos { grid-template-columns: 1fr; }
            .brand-font__style { grid-template-columns: 1fr; gap: 6px; }
            .brand-font__use { grid-template-columns: 1fr; }
            .brand-font__head { grid-template-columns: auto 1fr; }
            .brand-font__file { display: none; }
        }
        @media (max-width: 520px) { .brand-colours { grid-template-columns: 1fr 1fr; } }
    </style>
@endpush

@section('content')
<div class="brand" x-data="{ text: '', size: 120, blur: false }">

    {{-- Hero / wordmark --}}
    <section class="brand-hero">
        <p class="brand-label" style="color: rgba(250,244,234,.55)">Brand guide · v1</p>
        <div class="brand-mark">
            <div class="brand-mark__name">ANUBHOOTEE</div>
            <div class="brand-mark__road" aria-hidden="true"></div>
        </div>
        <div class="brand-mark__tag" lang="mr">अनुभूती · प्रवास · चव · संस्कृती</div>
        <div class="brand-hero__meta">
            <span>Logo · Colour · Type</span>
            <span>Experience India, one road at a time</span>
            <span>{{ date('Y') }}</span>
        </div>
    </section>

    {{-- Logo --}}
    <section class="brand-sec">
        <p class="brand-label">01 · Logo</p>
        <h2 class="brand-h">The road line</h2>
        <p class="brand-lead">ANUBHOOTEE in bold, widely spaced capitals over a dashed turmeric highway line. Keep clear space equal to the height of the letters on every side. Never stretch it or change the dash colour, except on saffron, where the line turns ink.</p>
        <div class="brand-logos">
            <div><span class="anu-logo"><span class="anu-logo__name">Anubhootee</span><span class="anu-logo__road"></span></span></div>
            <div class="on-ink"><span class="anu-logo"><span class="anu-logo__name">Anubhootee</span><span class="anu-logo__road"></span></span></div>
            <div class="on-saffron"><span class="anu-logo"><span class="anu-logo__name">Anubhootee</span><span class="anu-logo__road"></span></span></div>
        </div>
    </section>

    {{-- Colour --}}
    <section class="brand-sec" x-data="{ copied: '' }">
        <p class="brand-label">02 · Colour</p>
        <h2 class="brand-h">Earth, spice and night</h2>
        <p class="brand-lead">Photography leads; colour supports it. Saffron and turmeric are accents, never large backgrounds behind photos. Click a code to copy it.</p>
        <div class="brand-colours">
            @foreach ($colours as [$name, $hex, $use])
                <div class="brand-swatch">
                    <div class="brand-swatch__chip" style="background: {{ $hex }}; {{ $hex === '#FAF6EF' ? 'border-bottom: 1px solid var(--anu-line);' : '' }}"></div>
                    <div class="brand-swatch__info">
                        <b>{{ $name }}</b>
                        <button type="button" @click="navigator.clipboard?.writeText('{{ $hex }}'); copied = '{{ $hex }}'; setTimeout(() => copied = '', 1400)" x-text="copied === '{{ $hex }}' ? 'Copied' : '{{ $hex }}'">{{ $hex }}</button>
                        <small>{{ $use }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Core type --}}
    <section class="brand-sec">
        <p class="brand-label">03 · Core type</p>
        <h2 class="brand-h">On the website</h2>
        <p class="brand-lead">Every big heading on the website uses New York “New”. Everything you read uses the three free Google fonts below, which also cover Marathi. The other display fonts are for posters, thumbnails and social graphics.</p>
        <div class="brand-core brand-core--4">
            <div><span class="brand-label" style="margin:0">Headlines</span><div class="big" style="font-family: var(--font-display); text-transform: uppercase;">New York</div><p>Homepage chapters, page and story titles, section headings.</p></div>
            <div><span class="brand-label" style="margin:0">Interface &amp; body</span><div class="big" style="font-family: Inter; font-weight: 700; letter-spacing: -.05em;">Inter</div><p>Navigation, body text, labels, buttons.</p></div>
            <div><span class="brand-label" style="margin:0">मराठी</span><div class="big" style="font-family: var(--anu-mr); font-weight: 400;" lang="mr">मुक्ता</div><p>Every Devanagari word, Marathi pages and subtitles.</p></div>
            <div><span class="brand-label" style="margin:0">Quotes</span><div class="big" style="font-family: var(--font-quote); font-style: italic;">Newsreader</div><p>Pull-quotes in English stories.</p></div>
        </div>
    </section>

    {{-- Display fonts on photos --}}
    <section class="brand-sec" id="in-use">
        <p class="brand-label">04 · Display fonts in use</p>
        <h2 class="brand-h">On photographs</h2>
        <p class="brand-lead">Each display font on a photo, as it would appear on a poster, a thumbnail or an Instagram post. Click one to jump to its full specimen.</p>
        <div class="brand-posters">
            @foreach ($posters as [$id, $photo, $kicker, $head, $second, $mr])
                <a class="brand-poster" href="#font-{{ $id }}">
                    <img src="{{ asset('images/brand/'.$photo) }}" alt="" loading="lazy">
                    <span class="brand-poster__font">{{ $fontNames[$id] }}</span>
                    <span class="brand-poster__body">
                        <span class="brand-poster__kicker">{{ $kicker }}</span>
                        <span class="brand-poster__head" style="{{ $head[1] }}">{{ $head[0] }}</span>
                        <span class="brand-poster__second" style="{{ $second[1] }}">{{ $second[0] }}</span>
                        <span class="brand-poster__mr" lang="mr">{{ $mr }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Live tester --}}
    <div class="brand-tester">
        <input type="text" x-model="text" placeholder="Type to preview every display font…" aria-label="Preview text">
        <label>Size <input type="range" min="40" max="220" x-model.number="size"> <span x-text="size + 'px'"></span></label>
        <button type="button" :class="blur && 'is-on'" @click="blur = !blur">Contrue blur</button>
        <button type="button" @click="text = ''; size = 120; blur = false">Reset</button>
    </div>

    {{-- Display fonts --}}
    @foreach ($displayFonts as $i => $f)
        @php
            $first = explode('|', reset($f['styles']));
            $mainFamily = $f['id'] === 'contrue' ? null : $first[0];
            $mainWeight = $first[1] ?? 400;
        @endphp
        <section class="brand-font {{ in_array($f['id'], ['contrue', 'raga'], true) ? 'is-dark' : '' }}" id="font-{{ $f['id'] }}">
            <div class="brand-font__head">
                <span class="brand-font__num">{{ str_pad($i + 5, 2, '0', STR_PAD_LEFT) }}</span>
                <span class="brand-font__name">{{ $f['name'] }}<small>{{ $f['kind'] }}</small></span>
                <span class="brand-font__file">public/fonts/brand</span>
            </div>

            <div class="brand-font__sample"
                 @if ($f['id'] === 'contrue')
                     :style="{ fontFamily: blur ? 'Contrue Blackletter Blur' : 'Contrue Blackletter', fontSize: size + 'px' }"
                 @else
                     style="font-family: '{{ $mainFamily }}'; font-weight: {{ $mainWeight }}; {{ $f['case'] === 'upper' ? 'text-transform: uppercase;' : '' }}"
                     :style="{ fontSize: size + 'px' }"
                 @endif
                 x-text="text || @js($f['sample'])">{{ $f['sample'] }}</div>

            <div class="brand-font__styles">
                @foreach ($f['styles'] as $label => $family)
                    @php [$fam, $wt] = array_pad(explode('|', $family), 2, 400); @endphp
                    <div class="brand-font__style">
                        <span>{{ $label }}</span>
                        <div style="font-family: '{{ $fam }}'; font-weight: {{ $wt }}; {{ $f['case'] === 'upper' ? 'text-transform: uppercase;' : '' }}">{{ $alphabet }}</div>
                    </div>
                @endforeach
            </div>

            <div class="brand-font__use">
                <div><b>Use it for</b>{{ $f['use'] }}</div>
                <div><b>Pair with</b>{{ $f['pair'] }}. Latin only: set Marathi in Mukta.</div>
            </div>
        </section>
    @endforeach

    <section class="brand-sec">
        <p class="brand-note">Display fonts are licensed marketplace fonts. Keep each licence file with the project, and check that the licence covers web embedding before using a font on the live website. Arumma is not included: its download also contained unrelated software and should be re-downloaded from the original seller.</p>
    </section>
</div>
@endsection
