@extends('layouts.app')

@section('title', 'Projects — Studio Volume')

@section('content')

<header class="section" style="padding-bottom: clamp(2.5rem, 6vh, 4rem);">
    <div class="wrap">
        <span class="eyebrow rv">PROJECTS — 2023 / 2026</span>
        <h1 class="display rv" style="--rvd: 0.08s; margin-top: 1.4rem;">
            SELECTED <em>WORK.</em>
        </h1>
        <p class="lead rv" style="--rvd: 0.16s; margin-top: 1.8rem;">
            A collection of spaces shaped by material, light, context, and human experience —
            from private courtyard houses to public cultural buildings.
        </p>
    </div>
</header>

<section class="wrap" style="padding-bottom: clamp(4rem, 10vh, 8rem);">
    <div class="work-grid">
        @foreach ($projects as $i => $project)
            @php
                $sizes = ['size-xl', 'size-s', 'size-m', 'size-w', 'size-s', 'size-m'];
                $size = $sizes[$i % count($sizes)];
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
</section>

@endsection