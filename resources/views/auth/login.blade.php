<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Sign in · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card" aria-labelledby="auth-title">
            <a class="auth-brand" href="/" aria-label="Barangay Estanza home"><span class="auth-seal">BE</span><span>Barangay Estanza<small>Community services portal</small></span></a>
            <div class="auth-heading"><p class="auth-eyebrow">WELCOME BACK</p><h1 id="auth-title">Sign in</h1><p>Use your account to continue to the portal.</p></div>
            @if (session('status'))<div class="auth-notice" role="status">{{ session('status') }}</div>@endif
            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="auth-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <div class="auth-field"><div class="auth-label-row"><label for="password">Password</label><a href="{{ route('password.request') }}">Forgot password?</a></div><div class="password-input-wrap"><input id="password" name="password" type="password" autocomplete="current-password" data-password-toggle required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle-button><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>@error('password')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <label class="auth-remember"><input name="remember" type="checkbox" value="1"> Remember me</label>
                <button class="auth-submit" type="submit">Sign in</button>
            </form>
            <p class="auth-switch">Don’t have an account? <a href="{{ route('register') }}">Sign up</a></p>
        </section>
        <p class="auth-footer">Barangay Estanza · Community services portal</p>
    </main>
</body>
</html>
