<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Reset password · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card" aria-labelledby="auth-title">
            <a class="auth-brand" href="/" aria-label="Barangay Estanza home"><span class="auth-seal">BE</span><span>Barangay Estanza<small>Community services portal</small></span></a>
            <div class="auth-heading"><p class="auth-eyebrow">ACCOUNT RECOVERY</p><h1 id="auth-title">Set a new password</h1><p>Choose a new password for your account.</p></div>
            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="auth-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <div class="auth-field"><label for="password">New password</label><div class="password-input-wrap"><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" data-password-toggle required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle-button><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div><small class="auth-hint">Use at least 12 characters.</small>@error('password')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <div class="auth-field"><label for="password_confirmation">Confirm new password</label><div class="password-input-wrap"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" data-password-toggle required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle-button><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
                <button class="auth-submit" type="submit">Reset password</button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Back to sign in</a></p>
        </section>
        <p class="auth-footer">Barangay Estanza · Community services portal</p>
    </main>
</body>
</html>
