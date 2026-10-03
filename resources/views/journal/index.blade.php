@extends('layouts.app')

@section('title', 'Journal — Studio Volume')

@section('content')

<header class="section" style="padding-bottom: clamp(2.5rem, 6vh, 4rem);">
    <div class="wrap">
        <span class="eyebrow rv">JOURNAL</span>
        <h1 class="display rv" style="--rvd: 0.08s; margin-top: 1.4rem;">
            NOTES ON LIGHT,<br>MATERIAL &amp; <em>PRACTICE.</em>
        </h1>
        <p class="lead rv" style="--rvd: 0.16s; margin-top: 1.8rem;">
            Occasional writing from the studio — on the theory, craft and climate
            behind our buildings.
        </p>
    </div>
</header>

<section class="wrap" style="padding-bottom: clamp(4rem, 10vh, 8rem);">
    <div class="journal-grid">
        @foreach ($articles as $i => $article)
            <a class="journal-card rv" href="{{ route('journal.show', $article['slug']) }}" style="--rvd: {{ ($i % 3) * 0.08 }}s">
                <figure>
                    <img src="{{ ($article['image'] ?? '') ?: route('image', ['key' => 'journal-' . $article['slug']]) }}"
                         alt="{{ $article['title'] }}" loading="lazy" decoding="async">
                </figure>
                <div class="j-meta">
                    <span class="cat">{{ $article['category'] }}</span>
                    <span>{{ $article['date'] }}</span>
                </div>
                <h3>{{ $article['title'] }}</h3>
                <p>{{ $article['excerpt'] }}</p>
            </a>
        @endforeach
    </div>
</section>

@endsection