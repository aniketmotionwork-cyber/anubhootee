@extends('layouts.site')

@section('title', $category->label())
@section('description', $category->t('description') ?: $category->label())
@if ($category->coverUrl()) @section('og_image', $category->coverUrl()) @endif

@section('content')
    <section class="anu-archive">
        @if ($category->deva_word && $category->deva_word !== $category->label())
            <p class="anu-eyebrow" lang="mr">{{ $category->deva_word }}</p>
        @endif
        <h1>{{ $category->label() }}</h1>
        @if ($category->t('description'))
            <p class="anu-archive__desc">{{ $category->t('description') }}</p>
        @endif

        @php
            $types = $category->posts()->published()->distinct()->pluck('type');
            $current = request('type');
        @endphp
        @if ($types->count() > 1)
            <nav class="anu-filters" aria-label="{{ __('Filter') }}">
                <a href="{{ $category->url() }}" class="{{ $current ? '' : 'is-on' }}">{{ __('All') }}</a>
                @foreach (\App\Models\Post::TYPES_PLURAL as $key => $label)
                    @if ($types->contains($key))
                        <a href="{{ $category->url() }}?type={{ $key }}" class="{{ $current === $key ? 'is-on' : '' }}">{{ __($label) }}</a>
                    @endif
                @endforeach
            </nav>
        @endif

        @if ($posts->isEmpty())
            <p class="anu-empty">{{ __('Nothing here yet.') }}</p>
        @else
            <div class="anu-grid">
                @foreach ($posts as $post)
                    @include('partials.card', ['post' => $post])
                @endforeach
            </div>
            <div class="anu-pager">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
