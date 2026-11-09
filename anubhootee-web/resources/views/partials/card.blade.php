@php use App\Support\Lang; @endphp
<a class="anu-card" href="{{ $post->url() }}">
    <div class="anu-card__img">
        @if ($post->coverUrl())
            <img src="{{ $post->coverUrl() }}" alt="" loading="lazy">
        @endif
        @if ($post->isVideo())
            <span class="anu-card__play"><svg width="9" height="10" viewBox="0 0 9 10" fill="currentColor" aria-hidden="true"><path d="M0 0l9 5-9 5z"/></svg>{{ $post->typeLabel() }}</span>
        @endif
    </div>
    <p class="anu-card__meta">{{ $post->category?->label() }}@if ($post->location) · {{ $post->location }}@endif</p>
    <h3>{{ $post->t('title') }}</h3>
    @if ($post->subtitle && ! Lang::isMarathi())
        <p class="anu-card__sub" lang="mr">{{ $post->subtitle }}</p>
    @endif
</a>
