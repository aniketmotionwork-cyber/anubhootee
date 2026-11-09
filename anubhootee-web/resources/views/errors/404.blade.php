@extends('layouts.site')
@php use App\Support\Lang; @endphp

@section('title', __('Page not found'))

@section('content')
    <section class="anu-page anu-404">
        <p class="anu-eyebrow">404</p>
        <h1>{{ __('This road ends here.') }}</h1>
        <p style="font-size: 19px; color: var(--anu-ink-2);">{{ __('The page you were looking for has moved or never existed. Try a search, or head back to the chapters.') }}</p>
        <p style="margin-top: 32px;"><a class="anu-btn" href="{{ Lang::route('home') }}">{{ __('Back to the chapters') }} →</a></p>
    </section>
@endsection
