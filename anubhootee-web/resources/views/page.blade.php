@extends('layouts.site')

@section('title', $page->t('title'))
@section('description', \Illuminate\Support\Str::limit(strip_tags((string) $page->t('body')), 160))
@if ($page->coverUrl()) @section('og_image', $page->coverUrl()) @endif

@section('content')
    <section class="anu-page">
        <h1>{{ $page->t('title') }}</h1>
        @if ($page->coverUrl())
            <img class="anu-page__cover" src="{{ $page->coverUrl() }}" alt="">
        @endif
        <div class="anu-article">
            {{ $page->html('body') }}
        </div>
    </section>
@endsection
