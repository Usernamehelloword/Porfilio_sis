@extends('layouts.app')

@section('title', 'Contact — Studio Volume')

@section('content')

<section class="section contact-band" style="padding-top: calc(var(--nav-h) + clamp(3rem, 8vh, 6rem));">
    <div class="wrap">
        <div class="contact-grid">
            <div>
                <span class="eyebrow rv" style="color: var(--sunset-orange);">CONTACT</span>
                <h1 class="display rv" style="--rvd: 0.08s; margin-top: 1.4rem;">
                    HAVE A PROJECT<br>IN <em>MIND?</em>
                </h1>
                <p class="lead rv" style="--rvd: 0.16s; margin-top: 2rem;">
                    Let's discuss your next architectural project, interior, or development.
                    We reply to every inquiry within two working days.
                </p>

                <ul class="contact-list rv" style="--rvd: 0.22s;">
                    <li><span class="k">EMAIL</span><a href="mailto:studio@volume.example">studio@volume.example</a></li>
                    <li><span class="k">PHONE</span><a href="tel:+85512345678">+855 12 345 678</a></li>
                    <li><span class="k">STUDIO</span><span>Street 2404, Borey Peng Huoth,<br>Phnom Penh, Cambodia</span></li>
                    <li><span class="k">INSTAGRAM</span><a href="https://instagram.com" target="_blank" rel="noopener">@studio.volume</a></li>
                    <li><span class="k">LINKEDIN</span><a href="https://linkedin.com" target="_blank" rel="noopener">Studio Volume</a></li>
                </ul>
            </div>

            <div class="rv" style="--rvd: 0.2s;">
                @if (session('success'))
                    <div class="form-flash">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf

                    <div class="field">
                        <label for="name">NAME</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label for="email">EMAIL</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label for="project_type">PROJECT TYPE</label>
                        <select id="project_type" name="project_type">
                            <option value="">— Select —</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('project_type')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label for="budget">BUDGET</label>
                        <select id="budget" name="budget">
                            <option value="">— Select —</option>
                            @foreach ($budgets as $budget)
                                <option value="{{ $budget }}" @selected(old('budget') === $budget)>{{ $budget }}</option>
                            @endforeach
                        </select>
                        @error('budget')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label for="message">MESSAGE</label>
                        <textarea id="message" name="message" required>{{ old('message') }}</textarea>
                        @error('message')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <button class="btn light" type="submit" style="width: 100%; justify-content: center; margin-top: 0.6rem;">
                        SEND INQUIRY <span class="arrow">→</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection