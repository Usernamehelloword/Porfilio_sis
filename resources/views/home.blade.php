@extends('layouts.app')

@section('title', 'Studio Volume — Architecture, Interiors & Design')

@section('content')

{{-- ============================================================ hero --}}
@php $heroVideo = \App\Support\Settings::get('hero_video'); @endphp
<section class="hero">
    <div class="hero-media" data-parallax="0.06">
        @if ($heroVideo)
            <video src="{{ $heroVideo }}" autoplay muted loop playsinline poster="{{ $heroImage }}"></video>
        @else
            <img src="{{ $heroImage }}" alt="Modern concrete house facade in warm evening light" fetchpriority="high">
        @endif
    </div>

    <div class="hero-content">
        <p class="hero-eyebrow">{{ \App\Support\Settings::get('hero_subtitle') }}</p>

        <h1 class="hero-title">{!! \App\Support\Settings::get('hero_title') !!}</h1>

        <div class="hero-row">
            <p class="hero-desc">{{ \App\Support\Settings::get('hero_description') }}</p>

            <a class="btn light" href="{{ \App\Support\Settings::get('hero_button_link', '/projects') }}">
                {{ \App\Support\Settings::get('hero_button_text') }} <span class="arrow">→</span>
            </a>
        </div>

        <ul class="hero-meta">
            <li>PHNOM PENH — EST. 2014</li>
            <li>RESIDENTIAL · CULTURAL · HOSPITALITY</li>
            <li>12+ YEARS OF PRACTICE</li>
        </ul>
    </div>

    <div class="scroll-hint" aria-hidden="true">SCROLL</div>
</section>

{{-- ==================================================== selected work --}}
<section class="section" id="selected-work">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow">SELECTED WORK</span>
                <h2>Spaces shaped by<br>light &amp; <em>material.</em></h2>
            </div>
            <p class="lead rv" style="--rvd: 0.12s">
                A collection of spaces shaped by material, light, context, and human
                experience. Each project begins with the site and ends with the people
                who live in it.
            </p>
        </div>

        <div class="work-grid">
            @foreach ($projects as $i => $project)
                @php
                    $sizes = ['size-xl', 'size-s', 'size-m', 'size-w', 'size-s', 'size-m'];
                    $size = $sizes[$i % count($sizes)];
                    $cover = $project['slug'] . '-cover';
                @endphp

                <a class="work-card {{ $size }} rv-img" href="{{ route('projects.show', $project['slug']) }}">
                    <span class="work-num">{{ $project['index'] }}</span>
                    <span class="work-cta" aria-hidden="true">→</span>
                    <figure>
                        <img src="{{ \App\Support\Content::projectCoverUrl($project) }}"
                             alt="{{ $project['name'] }} — {{ $project['category'] }}"
                             loading="{{ $i < 2 ? 'eager' : 'lazy' }}" decoding="async">
                    </figure>
                    <span class="work-info">
                        <span>
                            <h3>{{ strtoupper($project['name']) }}</h3>
                            <span class="work-reveal">{{ $project['excerpt'] }}</span>
                        </span>
                        <span class="work-meta">
                            <span class="cat">{{ $project['category'] }}</span>
                            <span class="loc">{{ $project['location'] }} — {{ $project['year'] }}</span>
                        </span>
                    </span>
                </a>
            @endforeach
        </div>

        <div style="margin-top: clamp(3rem, 8vh, 5.5rem); text-align: center;" class="rv">
            <a class="text-link" href="{{ route('projects.index') }}">ALL PROJECTS <span class="arrow">→</span></a>
        </div>
    </div>
</section>

{{-- ======================================================= philosophy --}}
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

{{-- ======================================================== materials --}}
<section class="section">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow">MATERIAL LIBRARY</span>
                <h2>The palette we<br>build <em>with.</em></h2>
            </div>
            <p class="lead rv" style="--rvd: 0.12s">
                Concrete, stone, wood, glass, steel, brick, marble — chosen for how they
                age, how they feel in the hand, and how they hold the tropical light.
            </p>
        </div>

        <div class="mat-strip">
            @foreach ($materials as $i => $material)
                <div class="mat-card rv-img" style="--rvd: {{ $i * 0.06 }}s">
                    <img src="{{ route('image', ['key' => 'mat-' . $material['key']]) }}"
                         alt="{{ $material['name'] }} material sample" loading="lazy" decoding="async">
                    <span class="mat-label">
                        <strong>{{ $material['name'] }}</strong>
                        <span>{{ $material['tags'] }}</span>
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- -------------------------------------------------------------- about --}}
<section class="section" style="padding-top: 0;">
    <div class="wrap">
        <div class="about-grid">
            <figure class="about-portrait rv-img">
                <img src="{{ \App\Support\Settings::get('portrait_image') ? \App\Support\Content::imageUrl(\App\Support\Settings::get('portrait_image')) : route('image', ['key' => 'portrait']) }}"
                     alt="Portrait of the principal architect" loading="lazy">
                <figcaption class="mono-meta">
                    <span>{{ \App\Support\Settings::get('portrait_caption') }}</span>
                    <span>PHNOM PENH</span>
                </figcaption>
            </figure>

            <div>
                <span class="eyebrow terracotta rv">THE STUDIO</span>
                <h2 class="rv" style="font-size: clamp(2.2rem, 4.5vw, 4rem); margin-top: 1.4rem;">
                    {!! \App\Support\Settings::get('about_title') !!}
                </h2>
                <p class="lead rv" style="margin-top: 1.8rem;">
                    {{ \App\Support\Settings::get('about_description') }}
                </p>
                    single courtyard house to a public pavilion, our work begins with listening
                    — to the site, the client, and the daily rituals the building will host.
                </p>

                <div class="stats">
                    @foreach ($stats as $stat)
                        <div class="stat rv" style="--rvd: {{ $loop->index * 0.07 }}s">
                            <span class="value">{{ $stat['value'] }}</span>
                            <span class="label">{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="rv" style="margin-top: 3rem;">
                    <a class="btn" href="{{ route('about') }}">ABOUT THE STUDIO <span class="arrow">→</span></a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ----------------------------------------------------- quote band --}}
<section class="wrap rv" style="padding-block: clamp(3rem, 9vh, 7rem);">
    <p class="serif-quote" style="max-width: 30ch; margin: 0 auto; text-align: center;">
        “Architecture is the art of shaping how people experience space.”
    </p>
    <p class="mono-meta" style="text-align: center; margin-top: 1.6rem;">— STUDIO VOLUME, DESIGN CHARTER</p>
</section>

{{-- --------------------------------------------------------- drawings --}}
<section class="section drawings">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow">FROM THE DRAWING BOARD</span>
                <h2>Plans, sections<br>&amp; <em>elevations.</em></h2>
            </div>
            <p class="lead rv" style="--rvd: 0.12s">
                The working drawings behind the photographs — hover a drawing to reveal
                the project it belongs to.
            </p>
        </div>

        <div class="drawing-grid">
            @foreach ([['drawing-plan', 'GROUND FLOOR PLAN', 'Casa Forma'], ['drawing-section', 'SECTION A–A', 'Concrete House'], ['drawing-elevation', 'SOUTH ELEVATION', 'Light & Shadow'], ['drawing-site', 'SITE PLAN', 'Atelier Nord']] as $i => $d)
                <div class="drawing-item {{ $i === 0 ? 'wide' : '' }} rv-img" style="--rvd: {{ $i * 0.07 }}s">
                    <img src="{{ route('image', ['key' => $d[0]]) }}" alt="{{ $d[1] }} drawing" loading="lazy" decoding="async">
                    <div class="drawing-cap">
                        <span>{{ $d[1] }} — 1:100</span>
                        <span class="proj">{{ strtoupper($d[2]) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- --------------------------------------------------------- services --}}
<section class="section" id="services">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow">SERVICES</span>
                <h2>What we <em>do.</em></h2>
            </div>
            <p class="lead rv" style="--rvd: 0.12s">
                From the first site walk to the final handover — six ways we help shape
                a project.
            </p>
        </div>

        <div class="svc-list">
            @foreach (array_slice($services, 0, 4) as $service)
                <div class="svc-row rv" style="--rvd: {{ $loop->index * 0.06 }}s">
                    <span class="num">{{ $service['index'] }}</span>
                    <h3>{{ $service['title'] }}</h3>
                    <p>{{ $service['text'] }}</p>
                    <span class="go" aria-hidden="true">→</span>
                </div>
            @endforeach
        </div>

        <div class="rv" style="margin-top: 3rem;">
            <a class="text-link" href="{{ route('services') }}">ALL SERVICES <span class="arrow">→</span></a>
        </div>
    </div>
</section>

{{-- ---------------------------------------------------------- journal --}}
<section class="section" style="background: var(--off-white);">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow olive">JOURNAL</span>
                <h2>Notes on light,<br>material &amp; <em>practice.</em></h2>
            </div>
            <div class="rv" style="--rvd: 0.12s; justify-self: end; align-self: end;">
                <a class="text-link" href="{{ route('journal.index') }}">ALL ARTICLES <span class="arrow">→</span></a>
            </div>
        </div>

        <div class="journal-grid">
            @foreach (array_slice($articles, 0, 3) as $i => $article)
                <a class="journal-card rv" href="{{ route('journal.show', $article['slug']) }}" style="--rvd: {{ $i * 0.08 }}s">
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
    </div>
</section>

{{-- ---------------------------------------------------------- contact --}}
<section class="section contact-band">
    <div class="wrap">
        <div class="contact-grid">
            <div>
                <span class="eyebrow light rv" style="color: var(--sunset-orange);">NEW PROJECTS</span>
                <h2 class="display rv" style="--rvd: 0.1s; margin-top: 1.4rem;">
                    HAVE A PROJECT<br>IN <em>MIND?</em>
                </h2>
                <p class="lead rv" style="--rvd: 0.2s; margin-top: 2rem;">
                    Let's discuss your next architectural project, interior, or development.
                </p>
                <div class="rv" style="--rvd: 0.28s; margin-top: 2.6rem;">
                    <a class="btn light" href="{{ route('contact') }}">START A CONVERSATION <span class="arrow">→</span></a>
                </div>
            </div>

            <ul class="contact-list rv" style="--rvd: 0.24s;">
                <li><span class="k">EMAIL</span><a href="mailto:studio@volume.example">studio@volume.example</a></li>
                <li><span class="k">PHONE</span><a href="tel:+85512345678">+855 12 345 678</a></li>
                <li><span class="k">STUDIO</span><span>Street 2404, Borey Peng Huoth,<br>Phnom Penh, Cambodia</span></li>
                <li><span class="k">SOCIAL</span><span>Instagram · LinkedIn</span></li>
            </ul>
        </div>
    </div>
</section>

@endsection