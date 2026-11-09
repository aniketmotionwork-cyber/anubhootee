@extends('layouts.site')
@php use App\Support\Lang; @endphp

@section('title', $q ? __('Search') . ': ' . $q : __('Search'))

@section('content')
    <section class="anu-archive">
        <p class="anu-eyebrow">{{ __('Search') }}</p>
        <form action="{{ Lang::route('search') }}" method="get" role="search" style="border-bottom: 1px solid var(--anu-ink); max-width: 760px;">
            <input type="search" name="q" value="{{ $q }}" placeholder="{{ __('Search stories, places, food…') }}" aria-label="{{ __('Search') }}"
                   style="width: 100%; background: transparent; border: 0; font-size: clamp(32px, 5vw, 64px); letter-spacing: -.04em; padding: 6px 0; outline: none;">
        </form>
        @if ($q !== '')
            <p class="anu-archive__desc" style="margin-top: 16px;">{{ trans_choice(':count result|:count results', $posts->total(), ['count' => Lang::digits($posts->total())]) }}</p>
        @endif

        @if ($q !== '' && $posts->isEmpty())
            <p class="anu-empty">{{ __('Nothing matched. Try a place, a dish or a festival.') }}</p>
        @elseif ($posts->isNotEmpty())
            <div class="anu-grid">
                @foreach ($posts as $post)
                    @include('partials.card', ['post' => $post])
                @endforeach
            </div>
            <div class="anu-pager">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
