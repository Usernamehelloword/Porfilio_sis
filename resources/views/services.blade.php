@extends('layouts.app')

@section('title', 'Services — Studio Volume')

@section('content')

<header class="section" style="padding-bottom: clamp(2.5rem, 6vh, 4rem);">
    <div class="wrap">
        <span class="eyebrow rv">SERVICES</span>
        <h1 class="display rv" style="--rvd: 0.08s; margin-top: 1.4rem;">
            FROM FIRST SKETCH<br>TO <em>HANDOVER.</em>
        </h1>
        <p class="lead rv" style="--rvd: 0.16s; margin-top: 1.8rem;">
            Six services, one continuous way of working — architecture, interiors,
            planning, landscape, visualization and consultation.
        </p>
    </div>
</header>

<section class="wrap" style="padding-bottom: clamp(4rem, 10vh, 8rem);">
    <div class="svc-list">
        @foreach ($services as $service)
            <div class="svc-row rv" style="--rvd: {{ $loop->index * 0.06 }}s">
                <span class="num">{{ $service['index'] }}</span>
                <h3>{{ $service['title'] }}</h3>
                <p>{{ $service['text'] }}</p>
                <span class="go" aria-hidden="true">→</span>
            </div>
        @endforeach
    </div>
</section>

<section class="section philosophy">
    <div class="wrap">
        <div class="section-head">
            <div class="rv">
                <span class="eyebrow olive">HOW WE WORK</span>
                <h2>Every project,<br>the same <em>care.</em></h2>
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

<section class="section contact-band">
    <div class="wrap contact-grid">
        <div>
            <h2 class="display rv" style="--rvd: 0.1s; margin-top: 1.4rem;">
                HAVE A PROJECT<br>IN <em>MIND?</em>
            </h2>
            <div class="rv" style="--rvd: 0.2s; margin-top: 2.6rem;">
                <a class="btn light" href="{{ route('contact') }}">START A CONVERSATION <span class="arrow">→</span></a>
            </div>
        </div>
    </div>
</section>

@endsection