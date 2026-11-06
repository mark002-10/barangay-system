<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Forgot password · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card" aria-labelledby="auth-title">
            <a class="auth-brand" href="/" aria-label="Barangay Estanza home"><span class="auth-seal">BE</span><span>Barangay Estanza<small>Community services portal</small></span></a>
            <div class="auth-heading"><p class="auth-eyebrow">ACCOUNT RECOVERY</p><h1 id="auth-title">Forgot your password?</h1><p>Enter the email address on your account and we’ll send a reset link if it’s registered.</p></div>
            @if (session('status'))<div class="auth-notice" role="status">{{ session('status') }}</div>@endif
            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="auth-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <button class="auth-submit" type="submit">Send reset link</button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Back to sign in</a></p>
        </section>
        <p class="auth-footer">Barangay Estanza · Community services portal</p>
    </main>
</body>
</html>
