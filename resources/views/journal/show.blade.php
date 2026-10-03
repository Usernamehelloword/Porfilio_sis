@extends('layouts.app')

@section('title', $article['seo_title'] ?: ($article['title'] . ' — Journal'))
@section('meta_description', $article['seo_description'] ?: $article['excerpt'])

@section('content')

<article>
    <header class="section" style="padding-bottom: clamp(2rem, 5vh, 3.5rem);">
        <div class="wrap" style="max-width: 1100px;">
            <div class="j-meta rv" style="display: flex; gap: 1.2rem; font-size: 0.72rem; letter-spacing: 0.22em; text-transform: uppercase; color: var(--ink-40); margin-bottom: 1.4rem;">
                <span class="cat" style="color: var(--deep-teal); font-weight: 600;">{{ $article['category'] }}</span>
                <span>{{ $article['date'] }}</span>
            </div>
            <h1 class="rv" style="--rvd: 0.08s; font-size: clamp(2.2rem, 5.5vw, 4.2rem); max-width: 22ch;">
                {{ $article['title'] }}
            </h1>
        </div>
    </header>

    <div class="wrap" style="max-width: 1100px;">
        <figure class="p-fig full rv-img" style="margin: 0;">
            <figure>
                <img src="{{ ($article['image'] ?? '') ?: route('image', ['key' => 'journal-' . $article['slug']]) }}"
                     alt="{{ $article['title'] }}" fetchpriority="high">
            </figure>
        </figure>

        <div style="max-width: 68ch; margin-inline: auto; padding-block: clamp(3rem, 8vh, 5.5rem);">
            @if (is_array($article['body']))
                @foreach ($article['body'] as $i => $paragraph)
                    <p class="rv" style="--rvd: {{ $i * 0.08 }}s; font-size: 1.08rem; line-height: 1.85; color: var(--text-primary); margin: 0 0 1.8rem; {{ $i === 0 ? 'font-size: 1.25rem; font-family: var(--font-serif); line-height: 1.7;' : '' }}">
                        {{ $paragraph }}
                    </p>
                @endforeach
            @else
                <div style="font-size: 1.08rem; line-height: 1.85; color: var(--text-primary);">
                    {!! $article['body'] !!}
                </div>
            @endif

            <div class="rv" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <span class="mono-meta">STUDIO VOLUME — JOURNAL</span>
                <a class="text-link" href="{{ route('journal.index') }}">ALL ARTICLES <span class="arrow">→</span></a>
            </div>
        </div>
    </div>
</article>

@endsection