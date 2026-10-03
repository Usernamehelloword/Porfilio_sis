@extends('layouts.app')

@section('title', 'About — Studio Volume')

@section('content')

<header class="section" style="padding-bottom: clamp(2.5rem, 6vh, 4rem);">
    <div class="wrap">
        <span class="eyebrow rv">ABOUT THE STUDIO</span>
        <h1 class="display rv" style="--rvd: 0.08s; margin-top: 1.4rem;">
            {!! \App\Support\Settings::get('about_title') !!}
        </h1>
        <p class="lead rv" style="--rvd: 0.16s; margin-top: 1.8rem;">
            {{ \App\Support\Settings::get('about_description') }}
        </p>
    </div>
</header>

<section class="wrap" style="padding-bottom: clamp(4rem, 10vh, 8rem);">
    <div class="about-grid">
        <figure class="about-portrait rv-img">
            <img src="{{ \App\Support\Settings::get('portrait_image') ? \App\Support\Content::imageUrl(\App\Support\Settings::get('portrait_image')) : route('image', ['key' => 'portrait']) }}"
                 alt="Portrait of the principal architect">
            <figcaption class="mono-meta">
                <span>{{ \App\Support\Settings::get('portrait_caption') }}</span>
                <span>PHNOM PENH</span>
            </figcaption>
        </figure>

        <div>
            <p class="serif-quote rv">
                {{ \App\Support\Settings::get('about_quote') }}
            </p>

            <p class="lead rv" style="--rvd: 0.1s; margin-top: 2.4rem;">
                {{ \App\Support\Settings::get('about_body_1') }}
            </p>
            <p class="lead rv" style="--rvd: 0.16s; margin-top: 1.2rem;">
                {{ \App\Support\Settings::get('about_body_2') }}
            </p>

            <div class="stats">
                @foreach ($stats as $stat)
                    <div class="stat rv" style="--rvd: {{ $loop->index * 0.07 }}s">
                        <span class="value">{{ $stat['value'] }}</span>
                        <span class="label">{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section philosophy">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow olive">DESIGN PHILOSOPHY</span>
                <h2>Four constants<br>in every <em>project.</em></h2>
            </div>
        </div>

        <div class="phil-list">
            @foreach ($philosophy as $item)
                <div class="phil-row rv" style="--rvd: {{ $loop->index * 0.08 }}s">
                    <div class="phil-title">
                        <span class="idx">{{ $item['index'] }}</span>
                        <h3>{{ $item['title'] }}</h3>
                    </div>
                    <p>{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- studio environment image --}}
<section class="wrap section">
    <figure class="p-fig full rv-img" style="margin: 0;">
        <figure>
            <img src="{{ route('image', ['key' => 'about-studio']) }}"
                 alt="Inside the Studio Volume workspace — models, drawings and daylight"
                 loading="lazy" decoding="async">
        </figure>
        <figcaption>
            <span>THE STUDIO — PHNOM PENH</span>
            <span>STUDY MODELS &amp; PINNED DRAWINGS</span>
        </figcaption>
    </figure>

    <div class="section-head" style="margin-top: clamp(3rem, 8vh, 5rem); margin-bottom: 0;">
        <div class="rv">
            <span class="eyebrow">RECOGNITION</span>
            <h2>Selected<br><em>awards.</em></h2>
        </div>
        <div class="rv" style="--rvd: 0.12s;">
            <ul class="p-facts">
                <li><span class="k">2026</span><span>AR Emerging Architecture — Shortlist</span></li>
                <li><span class="k">2025</span><span>Dezeen Awards — House Rebirth, Winner</span></li>
                <li><span class="k">2025</span><span>World Architecture Festival — Future Projects</span></li>
                <li><span class="k">2024</span><span>Architizer A+ Awards — Unbuilt Cultural</span></li>
                <li><span class="k">2023</span><span>ARCASIA Award for Architecture — Silver</span></li>
            </ul>
        </div>
    </div>
</section>

{{-- ------------------------------------------------------------- team --}}
@if (! empty($designers))
<section class="section" style="background: var(--off-white);">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow olive">THE TEAM</span>
                <h2>Designers behind<br>the <em>work.</em></h2>
            </div>
        </div>

        <div class="journal-grid">
            @foreach ($designers as $designer)
                <article class="journal-card rv" style="--rvd: {{ $loop->index * 0.08 }}s">
                    <figure>
                        @if ($designer['photo'])
                            <img src="{{ $designer['photo'] }}" alt="{{ $designer['name'] }}" loading="lazy" decoding="async">
                        @else
                            <img src="{{ route('image', ['key' => 'portrait']) }}" alt="{{ $designer['name'] }}" loading="lazy" decoding="async">
                        @endif
                    </figure>
                    <div class="j-meta">
                        <span class="cat">{{ $designer['index'] }} — {{ $designer['position'] }}</span>
                        @if ($designer['years'])<span>{{ $designer['years'] }} YRS</span>@endif
                    </div>
                    <h3>{{ $designer['name'] }}</h3>
                    <p>{{ $designer['bio'] }}</p>
                    @if ($designer['specialization'])
                        <p style="font-size: .8rem; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-40);">{{ $designer['specialization'] }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
