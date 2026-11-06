<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Verification pending · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card auth-pending-card" aria-labelledby="auth-title">
            <a class="auth-brand" href="/" aria-label="Barangay Estanza home"><span class="auth-seal">BE</span><span>Barangay Estanza<small>Community services portal</small></span></a>
            <div class="auth-pending-icon" aria-hidden="true">…</div>
            <div class="auth-heading"><p class="auth-eyebrow">ACCOUNT REVIEW</p><h1 id="auth-title">Your account is waiting for review</h1><p>Thanks for signing up. The barangay office will check the resident information you shared. Once your account is approved, you can sign in to request certificates and report local concerns.</p></div>
            <div class="auth-notice">Your information is saved. You can sign out now and come back later to check your account.</div>
            <form class="auth-pending-logout" method="POST" action="{{ route('logout') }}">@csrf<button class="auth-submit" type="submit">Sign out</button></form>
        </section>
        <p class="auth-footer">Barangay Estanza · Community services portal</p>
    </main>
</body>
</html>
