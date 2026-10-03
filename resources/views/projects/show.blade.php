@extends('layouts.app')

@section('title', $project['seo_title'] ?: ($project['name'] . ' — ' . \App\Support\Settings::get('studio_name')))
@section('meta_description', $project['seo_description'] ?: $project['excerpt'])


@section('content')

<article>

{{-- ------------------------------------------------------------- hero --}}
<header class="project-hero wrap">
    <span class="eyebrow terracotta">{{ strtoupper($project['category']) }}</span>
    <h1 class="p-title">{{ strtoupper($project['name']) }}</h1>

    <div class="p-meta-row rv">
        <div class="item"><span class="k">Location</span><span class="v">{{ $project['location'] }}</span></div>
        <div class="item"><span class="k">Year</span><span class="v">{{ $project['year'] }}</span></div>
        <div class="item"><span class="k">Type</span><span class="v">{{ $project['type'] }}</span></div>
        <div class="item"><span class="k">Status</span><span class="v">{{ $project['status'] }}</span></div>
    </div>
</header>

<div class="p-cover rv-img">
    <img src="{{ $coverUrl }}"
         alt="{{ $project['name'] }} — cover image" fetchpriority="high">
</div>

{{-- ----------------------------------------------------------- concept --}}
<section class="wrap p-body">
    <aside class="rv">
        <span class="eyebrow">PROJECT INFORMATION</span>

        <ul class="p-facts" style="margin-top: 1.6rem;">
            <li><span class="k">Client</span><span>{{ $project['client'] }}</span></li>
            <li><span class="k">Location</span><span>{{ $project['location'] }}</span></li>
            <li><span class="k">Year</span><span>{{ $project['year'] }}</span></li>
            <li><span class="k">Area</span><span>{{ $project['area'] }}</span></li>
            <li><span class="k">Type</span><span>{{ $project['type'] }}</span></li>
            <li><span class="k">Status</span><span>{{ $project['status'] }}</span></li>
        </ul>

        <div class="p-materials">
            @foreach ($project['materials'] as $material)
                <span class="chip">{{ $material }}</span>
            @endforeach
        </div>
    </aside>

    <div class="p-concept rv" style="--rvd: 0.12s;">
        <span class="eyebrow">CONCEPT</span>
        @if ($detail)
            @if (trim((string) $detail->overview))
                <div class="p-section" style="margin-top: 1.6rem;">
                    <span class="eyebrow olive" style="font-size: .62rem;">OVERVIEW</span>
                    {!! $detail->overview !!}
                </div>
            @endif
            @if (trim((string) $detail->concept))
                <div style="margin-top: 1.6rem;">{!! $detail->concept !!}</div>
            @endif
            @if (trim((string) $detail->design_approach))
                <div class="p-section" style="margin-top: 1.6rem;">
                    <span class="eyebrow olive" style="font-size: .62rem;">DESIGN APPROACH</span>
                    {!! $detail->design_approach !!}
                </div>
            @endif
            @if (trim((string) $detail->challenges))
                <div class="p-section" style="margin-top: 1.6rem;">
                    <span class="eyebrow olive" style="font-size: .62rem;">CHALLENGES</span>
                    {!! $detail->challenges !!}
                </div>
            @endif
            @if (trim((string) $detail->solution))
                <div class="p-section" style="margin-top: 1.6rem;">
                    <span class="eyebrow olive" style="font-size: .62rem;">SOLUTION</span>
                    {!! $detail->solution !!}
                </div>
            @endif
        @else
            @foreach ($project['concept'] as $i => $paragraph)
                <p style="margin-top: {{ $i === 0 ? '1.6rem' : '0' }};">{{ $paragraph }}</p>
            @endforeach
        @endif
    </div>
</section>

{{-- ----------------------------------------------------------- gallery --}}
<div class="wrap p-gallery">
    @foreach ($gallery as $i => [$key, $caption, $kind, $span])
        <figure class="p-fig {{ $kind === 'drawing' ? 'drawing' : '' }} {{ $span }} rv-img" style="--rvd: {{ ($i % 3) * 0.08 }}s">
            @if ($kind === 'drawing')
                <div class="frame">
                    <img src="{{ route('image', ['key' => $key]) }}" alt="{{ $caption }}" loading="lazy" decoding="async">
                </div>
            @else
                <figure>
                    <img src="{{ route('image', ['key' => $key]) }}" alt="{{ $caption }}" loading="lazy" decoding="async">
                </figure>
            @endif
            <figcaption>
                <span>{{ $caption }}</span>
                <span>{{ strtoupper($kind) }} — {{ sprintf('%02d', $i + 1) }}</span>
            </figcaption>
        </figure>
    @endforeach
</div>

{{-- -------------------------------------------------------- next project --}}
@if ($next)
    <a class="next-project" href="{{ route('projects.show', $next['slug']) }}">
        <span class="np-k">NEXT PROJECT — {{ $next['index'] }}</span>
        <h3>{{ strtoupper($next['name']) }}</h3>
    </a>
@endif

</article>

@endsection