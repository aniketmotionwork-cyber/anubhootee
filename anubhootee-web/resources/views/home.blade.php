@extends('layouts.site')
@php use App\Support\Lang; @endphp

@section('overlay', '1')
@section('no_footer', '1')

@section('content')
    @if ($chapters->isEmpty())
        <section class="anu-archive anu-404" style="padding-top: 160px;">
            <p class="anu-eyebrow">{{ config('app.name') }}</p>
            <h1>{{ __('Experience India, one road at a time') }}</h1>
            <p class="anu-archive__desc">{{ __('No chapters yet. In the admin, open Sections & chapters, tick “Show on homepage” and add a cover photo.') }}</p>
        </section>
    @else
        <div class="anu-showcase">
            <div class="anu-sc-loader" aria-hidden="true"><div><div class="anu-sc-loader__word">अनुभूती</div><div class="anu-sc-loader__road"></div></div></div>

            <div class="anu-sc-stage">
                <canvas class="anu-sc-gl" aria-hidden="true"></canvas>
                <div class="anu-sc-fallback" aria-hidden="true"></div>
                <div class="anu-sc-shade" aria-hidden="true"></div>
            </div>

            {{--
                Depth effect (iPhone lock-screen style). Layers, back to front:
                photo → big titles (--back) → subject cutout (.anu-sc-fg) → labels & links (--front) → bars.
                Both slide layers have identical layout, so the title lines up with the rest.
            --}}
            @foreach (['back', 'front'] as $layer)
                <div class="anu-sc-slides anu-sc-slides--{{ $layer }}" @if ($layer === 'back') aria-hidden="true" @else role="list" @endif>
                    @foreach ($chapters as $i => $ch)
                        <div class="anu-sc-slide {{ $i === 0 ? 'is-active' : '' }}" @if ($layer === 'front') role="listitem" @endif>
                            <div class="anu-sc-meta"><span class="anu-sc-num">{{ Lang::digits(str_pad($i + 1, 2, '0', STR_PAD_LEFT)) }}</span><span class="anu-sc-bar" aria-hidden="true"></span><span>{{ $ch['tag'] }}</span></div>
                            <h2 class="anu-sc-title" aria-label="{{ $ch['title'] }}">@foreach (mb_str_split($ch['title']) as $letter)<span class="anu-ch" aria-hidden="true">{{ $letter === ' ' ? "\u{00A0}" : $letter }}</span>@endforeach</h2>
                            @if ($ch['deva'])<div class="anu-sc-deva" lang="mr">{{ $ch['deva'] }}</div>@endif
                            <a class="anu-sc-more" href="{{ $ch['url'] }}" @if ($layer === 'back') tabindex="-1" @endif><span class="anu-sc-line" aria-hidden="true"></span>{{ $ch['cta'] }}</a>
                        </div>
                    @endforeach
                </div>
                @if ($layer === 'back')
                    <div class="anu-sc-fg" aria-hidden="true">
                        <canvas class="anu-sc-fg-gl"></canvas>
                        <div class="anu-sc-fg-fallback"></div>
                    </div>
                @endif
            @endforeach

            <div class="anu-sc-credit"></div>

            <div class="anu-sc-bottom">
                <div class="anu-sc-counter" aria-hidden="true"><b>01</b> / {{ str_pad($chapters->count(), 2, '0', STR_PAD_LEFT) }}</div>
                <nav class="anu-sc-progress" aria-label="{{ __('Chapters') }}">
                    @foreach ($chapters as $i => $ch)
                        <button type="button" class="{{ $i === 0 ? 'is-on' : '' }}" aria-label="{{ $ch['tag'] }}"><i>{{ Lang::digits(str_pad($i + 1, 2, '0', STR_PAD_LEFT)) }}</i><em>{{ $ch['tag'] }}</em></button>
                    @endforeach
                </nav>
                <div class="anu-sc-hint" aria-hidden="true"><span class="anu-sc-hint__icon"></span>{{ Lang::isMarathi() ? 'ओढा' : 'Drag' }}</div>
            </div>

            <script type="application/json" class="anu-sc-data">{!! json_encode(['chapters' => $chapters, 'photo' => __('Photo')], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        </div>
    @endif
@endsection
