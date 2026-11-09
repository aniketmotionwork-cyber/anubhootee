@extends('layouts.site')
@php
    use App\Support\Lang;
    $meta = array_filter([$post->location, Lang::digits(trans_choice(':count min read', $post->readingMinutes(), ['count' => $post->readingMinutes()]))]);
    $embed = $post->embedUrl();
    $isReel = str_contains((string) $post->video_url, 'instagram.com');
@endphp

@section('title', $post->t('title'))
@section('description', $post->t('summary') ?: $post->subtitle ?: $post->t('title'))
@section('og_type', 'article')
@section('overlay', $post->coverUrl() ? '1' : '0')
@if ($post->coverUrl()) @section('og_image', $post->coverUrl()) @endif

@section('main_class', 'anu-story')

@section('content')
    @php $fg = $post->depthUrl(); @endphp
    <section class="anu-hero {{ $post->coverUrl() ? '' : 'no-image' }} {{ $fg ? 'has-depth' : '' }}">
        @if ($post->coverUrl())
            <img src="{{ $post->coverUrl() }}" alt="" fetchpriority="high">
        @endif
        @if ($fg)
            {{-- Depth effect: title behind the photo's subject (back layer), labels in front --}}
            <div class="anu-hero__inner anu-hero__inner--back" aria-hidden="true">
                <div class="anu-kicker"><span>{{ $post->category?->label() }}</span><span class="anu-kicker__bar"></span>@if ($post->type !== 'story')<span>{{ $post->typeLabel() }}</span><span class="anu-kicker__bar"></span>@endif<span>{{ implode(' · ', $meta) }}</span></div>
                <h1>{{ $post->t('title') }}</h1>
                @if ($post->subtitle)<p class="anu-hero__sub" lang="mr">{{ $post->subtitle }}</p>@endif
            </div>
            <div class="anu-hero__fg" aria-hidden="true">
                <img src="{{ $fg }}" alt="">
                <i style="-webkit-mask-image: url('{{ $fg }}'); mask-image: url('{{ $fg }}');"></i>
            </div>
        @endif
        <div class="anu-hero__inner anu-hero__inner--front">
            <div class="anu-kicker">
                @if ($post->category)
                    <a href="{{ $post->category->url() }}">{{ $post->category->label() }}</a>
                    <span class="anu-kicker__bar" aria-hidden="true"></span>
                @endif
                @if ($post->type !== 'story')<span>{{ $post->typeLabel() }}</span><span class="anu-kicker__bar" aria-hidden="true"></span>@endif
                <span>{{ implode(' · ', $meta) }}</span>
            </div>
            <h1>{{ $post->t('title') }}</h1>
            @if ($post->subtitle)
                <p class="anu-hero__sub" lang="mr">{{ $post->subtitle }}</p>
            @endif
        </div>
        @if ($post->cover_credit)
            <p class="anu-hero__credit">{{ __('Photo') }}: {{ $post->cover_credit }}</p>
        @endif
    </section>

    <div class="anu-byline">
        <div class="anu-byline__who">
            <div class="anu-byline__avatar" aria-hidden="true">अ</div>
            <div>
                <b>{{ $post->author?->name ?? config('app.name') }}</b>
                <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ Lang::date($post->published_at) }}</time>
            </div>
        </div>
        <div class="anu-share">
            <a href="https://wa.me/?text={{ rawurlencode($post->t('title').' '.$post->url()) }}" target="_blank" rel="noopener">WhatsApp</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($post->url()) }}" target="_blank" rel="noopener">Facebook</a>
            <button type="button" data-anu-copy="{{ $post->url() }}" data-copied="{{ __('Copied') }}">{{ __('Copy link') }}</button>
        </div>
    </div>

    @if ($post->isVideo())
        <div class="anu-wide">
            @if ($embed)
                <div class="anu-video {{ $isReel ? 'anu-video--reel' : '' }}">
                    <iframe src="{{ $embed }}" title="{{ $post->t('title') }}" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            @else
                <div class="anu-video anu-video--empty">
                    <div><b>▶ {{ __('Video coming soon') }}</b><span>{{ __('Add the YouTube or Instagram link in the admin') }}</span></div>
                </div>
            @endif
        </div>
    @endif

    <article class="anu-article">
        {{ $post->html('body') }}

        @if (! empty($post->facts))
            <dl class="anu-route" style="--cols: {{ min(4, count($post->facts)) }}">
                @foreach ($post->facts as $fact)
                    <div><dt>{{ $fact['label'] ?? '' }}</dt><dd>{{ $fact['value'] ?? '' }}</dd></div>
                @endforeach
            </dl>
        @endif

        @if (! empty($post->tips))
            <div class="anu-tips">
                <h3>{{ __('Important tips') }}</h3>
                <ol>
                    @foreach ($post->tips as $tip)
                        <li><strong>{{ $tip['title'] ?? '' }}</strong> {{ $tip['text'] ?? '' }}</li>
                    @endforeach
                </ol>
            </div>
        @endif
    </article>

    @if (! empty($post->tags))
        <div class="anu-tags">
            @foreach ($post->tags as $tag)
                <a href="{{ Lang::route('search') }}?q={{ urlencode($tag) }}">#{{ $tag }}</a>
            @endforeach
        </div>
    @endif

    @if ($next)
        <a class="anu-next" href="{{ $next->url() }}">
            @if ($next->coverUrl())<img src="{{ $next->coverUrl() }}" alt="" loading="lazy">@endif
            <span class="anu-next__inner">
                <small>{{ __('Next story') }}@if ($next->category) · {{ $next->category->label() }}@endif</small>
                <span class="anu-next__title">{{ $next->t('title') }} <span class="anu-next__arrow" aria-hidden="true">→</span></span>
            </span>
        </a>
    @endif
@endsection
