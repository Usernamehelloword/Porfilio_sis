<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Studio Volume Admin</title>
    <link rel="icon" href="{{ asset('image/logo_1.png') }}" type="image/png">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="login-body">
    <div class="login-card">
        <span class="login-mark">SV</span>
        <h1>STUDIO<b>·</b>VOLUME</h1>
        <p class="login-sub">Architecture Portfolio — Admin Panel</p>

        @if ($errors->any())
            <div class="flash flash-error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}">
            @csrf
            <div class="field">
                <label for="email">EMAIL</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>
            <div class="field">
                <label for="password">PASSWORD</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>
            <label class="check">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">SIGN IN</button>
        </form>

        <p class="login-foot"><a href="{{ route('home') }}">← Back to website</a></p>
    </div>
</body>
</html>
