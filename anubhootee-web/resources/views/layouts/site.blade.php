@php
    use App\Support\Lang;
    $title = trim($__env->yieldContent('title'));
    $fullTitle = $title ? $title.' – '.config('app.name') : config('app.name').' · '.__('Experience India, one road at a time');
    $description = trim($__env->yieldContent('description')) ?: __('Stories, food, festivals and road trips from India, in English and Marathi.');
    $image = trim($__env->yieldContent('og_image')) ?: asset('images/og-default.jpg');
    $overlay = trim($__env->yieldContent('overlay')) === '1';
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="en" href="{{ Lang::switchUrl('en') }}">
    <link rel="alternate" hreflang="mr" href="{{ Lang::switchUrl('mr') }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:title" content="{{ $title ?: config('app.name') }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 200) }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#14110E">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preload" href="{{ asset('fonts/brand/new-york-new.otf') }}" as="font" type="font/otf" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&family=Mukta:wght@300;400;500;700&family=Tiro+Devanagari+Marathi&family=Newsreader:ital,opsz,wght@1,6..72,400&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="{{ Lang::isMarathi() ? 'anu-lang-mr' : '' }}" x-data="{ menu: false, search: false }" @keydown.escape.window="menu = false; search = false">
    <a class="anu-skip" href="#main">{{ __('Skip to content') }}</a>

    <header class="anu-header {{ $overlay ? 'is-overlay' : '' }}">
        <a class="anu-logo" href="{{ Lang::route('home') }}" rel="home" aria-label="{{ config('app.name') }}">
            <span class="anu-logo__name">{{ config('app.name') }}</span>
            <span class="anu-logo__road" aria-hidden="true"></span>
        </a>
        <div class="anu-header-right">
            <nav class="anu-nav" aria-label="{{ __('Main') }}">
                @foreach ($navCategories as $c)
                    <a href="{{ $c->url() }}" @if (request()->is('category/'.$c->slug, 'mr/category/'.$c->slug)) aria-current="page" @endif>{{ $c->label() }}</a>
                @endforeach
                @foreach ($menuPages as $p)
                    <a href="{{ $p->url() }}" @if (request()->is($p->slug, 'mr/'.$p->slug)) aria-current="page" @endif>{{ $p->t('title') }}</a>
                @endforeach
            </nav>
            <button type="button" class="anu-icon-btn" @click="search = true; $nextTick(() => $refs.q.focus())" aria-label="{{ __('Search') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </button>
            <div class="anu-lang" aria-label="{{ __('Language') }}">
                <svg class="anu-lang__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.6 3.8 9s-1.3 6.4-3.8 9c-2.5-2.6-3.8-5.6-3.8-9S9.5 5.6 12 3z"/></svg>
                @foreach (Lang::LOCALES as $code => $label)
                    <a href="{{ Lang::switchUrl($code) }}" lang="{{ $code }}" hreflang="{{ $code }}" class="{{ app()->getLocale() === $code ? 'is-current' : '' }}" @if (app()->getLocale() === $code) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>
            <button type="button" class="anu-icon-btn anu-burger" @click="menu = true" aria-label="{{ __('Menu') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 8h16M4 16h16"/></svg>
            </button>
        </div>
    </header>

    {{-- Mobile menu --}}
    <div class="anu-menu" x-show="menu" x-cloak x-transition.opacity role="dialog" aria-modal="true" aria-label="{{ __('Menu') }}">
        <div class="anu-menu__top">
            <span class="anu-logo"><span class="anu-logo__name">{{ config('app.name') }}</span><span class="anu-logo__road"></span></span>
            <button type="button" class="anu-icon-btn" @click="menu = false" aria-label="{{ __('Close') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <nav>
            @foreach ($navCategories as $c)
                <a href="{{ $c->url() }}">{{ $c->label() }} @if ($c->deva_word)<small lang="mr">{{ $c->deva_word }}</small>@endif</a>
            @endforeach
            @foreach ($menuPages as $p)
                <a href="{{ $p->url() }}">{{ $p->t('title') }}</a>
            @endforeach
        </nav>
        <div class="anu-menu__foot">
            <div class="anu-lang">
                @foreach (Lang::LOCALES as $code => $label)
                    <a href="{{ Lang::switchUrl($code) }}" lang="{{ $code }}" class="{{ app()->getLocale() === $code ? 'is-current' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
            <a href="{{ Lang::route('contact') }}">{{ __('Contact') }}</a>
        </div>
    </div>

    {{-- Search overlay --}}
    <div class="anu-search" x-show="search" x-cloak x-transition.opacity role="dialog" aria-modal="true" aria-label="{{ __('Search') }}">
        <button type="button" class="anu-icon-btn" @click="search = false" aria-label="{{ __('Close') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
        <form action="{{ Lang::route('search') }}" method="get" role="search">
            <input x-ref="q" type="search" name="q" placeholder="{{ __('Search stories, places, food…') }}" aria-label="{{ __('Search') }}">
            <p>{{ __('Press Enter to search') }}</p>
        </form>
    </div>

    <main id="main" class="@yield('main_class')">
        @yield('content')
    </main>

    @unless (trim($__env->yieldContent('no_footer')) === '1')
        <footer class="anu-footer">
            <div class="anu-footer__grid">
                <div class="anu-footer__intro">
                    <h2>{{ __('Stories you can taste.') }}<br><span lang="mr" style="font-family: var(--anu-mr); font-weight: 300;">अनुभूती · प्रवास · चव · संस्कृती</span></h2>
                    <p style="margin: 0 0 14px; font-size: 15px;">{{ __('New stories and films, once a month. No spam.') }}</p>
                    <form class="anu-sub" action="{{ Lang::route('subscribe') }}" method="post">
                        @csrf
                        <input type="email" name="email" required placeholder="{{ __('Your email') }}" aria-label="{{ __('Your email') }}">
                        <button type="submit">{{ __('Subscribe') }}</button>
                    </form>
                    @if (session('subscribed'))<p class="anu-footer__note" role="status">{{ session('subscribed') }}</p>@endif
                    @error('email', 'subscribe')<p class="anu-footer__note" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <h3>{{ __('Explore') }}</h3>
                    <ul>
                        @foreach ($navCategories as $c)
                            <li><a href="{{ $c->url() }}">{{ $c->label() }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h3>{{ __('Follow') }}</h3>
                    <ul>
                        @foreach (config('anubhootee.social') as $name => $url)
                            @if ($url)<li><a href="{{ $url }}" target="_blank" rel="noopener">{{ $name }}</a></li>@endif
                        @endforeach
                        @foreach ($menuPages as $p)
                            <li><a href="{{ $p->url() }}">{{ $p->t('title') }}</a></li>
                        @endforeach
                        <li><a href="{{ Lang::route('contact') }}">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="anu-footer__bottom">
                <span>&copy; {{ Lang::digits(date('Y')) }} {{ config('app.name') }}</span>
                <nav aria-label="{{ __('Policies') }}">
                    @foreach ($footerPages as $p)
                        <a href="{{ $p->url() }}">{{ $p->t('title') }}</a>
                    @endforeach
                </nav>
            </div>
        </footer>
    @endunless

    @stack('scripts')
</body>
</html>
