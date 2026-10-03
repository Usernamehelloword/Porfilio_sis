@extends('admin.layout')

@section('title', 'Appearance')
@section('page_title', 'Appearance')

@section('content')
<form method="POST" action="{{ route('admin.appearance.update') }}">
    @csrf

    <div class="card">
        <h3 class="card-title">Colors</h3>
        <div class="form-grid">
            @foreach ([
                'color_primary' => 'Primary Color',
                'color_secondary' => 'Secondary Color',
                'color_accent' => 'Accent Color',
                'color_background' => 'Background Color',
                'color_text' => 'Text Color',
            ] as $key => $label)
                <div class="field">
                    <label>{{ strtoupper($label) }}</label>
                    <div class="color-row">
                        <input type="color" name="{{ $key }}" value="{{ old($key, \App\Support\Settings::get($key)) }}" oninput="this.nextElementSibling.value=this.value">
                        <input type="text" value="{{ old($key, \App\Support\Settings::get($key)) }}" readonly>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="hint">Default architecture theme — white, black, gray, beige, muted olive, terracotta.</p>
    </div>

    <div class="card">
        <h3 class="card-title">Typography</h3>
        <div class="form-grid">
            <div class="field">
                <label>HEADING FONT</label>
                <select name="font_heading">
                    @foreach ($fonts as $value => $label)
                        <option value="{{ $value }}" @selected(old('font_heading', \App\Support\Settings::get('font_heading')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>BODY FONT</label>
                <select name="font_body">
                    @foreach ($fonts as $value => $label)
                        <option value="{{ $value }}" @selected(old('font_body', \App\Support\Settings::get('font_body')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Buttons</h3>
        <div class="form-grid">
            <div class="field">
                <label>BUTTON STYLE</label>
                <select name="button_style">
                    @foreach ($buttonStyles as $style)
                        <option value="{{ $style }}" @selected(old('button_style', \App\Support\Settings::get('button_style')) === $style)>{{ ucfirst($style) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>BUTTON ANIMATION</label>
                <select name="button_animation">
                    @foreach ($buttonAnimations as $animation)
                        <option value="{{ $animation }}" @selected(old('button_animation', \App\Support\Settings::get('button_animation')) === $animation)>{{ ucfirst($animation) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <button class="btn-primary" type="submit">Save Appearance</button>
</form>
@endsection
