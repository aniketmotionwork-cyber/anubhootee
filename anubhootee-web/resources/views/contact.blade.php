@extends('layouts.site')
@php use App\Support\Lang; @endphp

@section('title', $page?->t('title') ?? __('Contact'))

@section('content')
    <section class="anu-page">
        <h1>{{ $page?->t('title') ?? __('Contact') }}</h1>

        @if ($page)
            <div class="anu-article">{{ $page->html('body') }}</div>
        @endif

        @if (session('status'))
            <p class="anu-alert" role="status" style="margin-top: 32px;">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="anu-alert anu-alert--error" role="alert" style="margin-top: 32px;">{{ __('Please check the form below.') }}</p>
        @endif

        <form class="anu-form" action="{{ Lang::route('contact.send') }}" method="post">
            @csrf
            <div class="anu-form__row">
                <label>{{ __('Name') }}
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                    @error('name')<span class="anu-field-error">{{ $message }}</span>@enderror
                </label>
                <label>{{ __('Email') }}
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email">
                    @error('email')<span class="anu-field-error">{{ $message }}</span>@enderror
                </label>
            </div>
            <label>{{ __('Subject') }}
                <input type="text" name="subject" value="{{ old('subject') }}" maxlength="190">
            </label>
            <label>{{ __('Message') }}
                <textarea name="message" rows="7" required maxlength="5000">{{ old('message') }}</textarea>
                @error('message')<span class="anu-field-error">{{ $message }}</span>@enderror
            </label>
            <label class="anu-hp" aria-hidden="true">Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            <button class="anu-btn" type="submit">{{ __('Send message') }} →</button>
        </form>
    </section>
@endsection
