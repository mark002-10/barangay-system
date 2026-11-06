<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="Find Barangay Estanza community services, request a certificate, report a local concern, and follow barangay updates.">
    <title>Barangay Estanza · Community Services</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-home">
    <div class="public-home-shell">
        <header class="public-header">
            <a class="public-brand" href="/" aria-label="Barangay Estanza home">
                <span class="public-seal" aria-hidden="true">BE</span>
                <span><strong>Barangay Estanza</strong><small>Community Services</small></span>
            </a>
            <nav class="public-nav" aria-label="Account">
                @auth
                    <a class="public-nav-link" href="{{ route('dashboard') }}">Open your portal</a>
                @else
                    <a class="public-nav-link" href="{{ route('login') }}">Sign in</a>
                    <a class="public-nav-button" href="{{ route('register') }}">Create an account</a>
                @endauth
            </nav>
        </header>

        <main>
            <section class="public-hero" aria-labelledby="public-title">
                <div class="public-hero-copy">
                    <p class="public-eyebrow">HERE FOR OUR COMMUNITY</p>
                    <h1 id="public-title">Barangay services, a little easier.</h1>
                    <p class="public-lead">Request a certificate, tell us about a local concern, and keep up with community updates—all in one place.</p>
                    <div class="public-hero-actions">
                        @auth
                            <a class="public-primary-button" href="{{ route('dashboard') }}">Go to my portal</a>
                        @else
                            <a class="public-primary-button" href="{{ route('register') }}">Get started</a>
                            <a class="public-secondary-link" href="{{ route('login') }}">Already have an account? Sign in</a>
                        @endauth
                    </div>
                    <p class="public-quiet-note">New resident accounts are reviewed by a barangay administrator before services become available.</p>
                </div>
                <aside class="public-next-card" aria-label="What happens next">
                    <span class="public-next-label">A SIMPLE WAY TO GET STARTED</span>
                    <h2>What happens next?</h2>
                    <ol class="public-steps">
                        <li><span>1</span><div><strong>Share your details</strong><small>Create an account with your resident information.</small></div></li>
                        <li><span>2</span><div><strong>Wait for account review</strong><small>The barangay office checks your information.</small></div></li>
                        <li><span>3</span><div><strong>Use community services</strong><small>Request certificates and follow up on reports.</small></div></li>
                    </ol>
                </aside>
            </section>

            <section class="public-services" aria-labelledby="services-title">
                <div class="public-section-heading"><p class="public-eyebrow">HOW WE CAN HELP</p><h2 id="services-title">Services for everyday needs</h2><p>Sign in or create a resident account to get started.</p></div>
                <div class="public-service-grid">
                    <article class="public-service-card"><span class="public-service-icon">▤</span><h3>Request a certificate</h3><p>Tell the office which certificate you need and what it’s for. You can check its progress in your account.</p></article>
                    <article class="public-service-card"><span class="public-service-icon public-report-icon">!</span><h3>Report a local concern</h3><p>Share what happened and where. The barangay team can review the details and post updates.</p></article>
                    <article class="public-service-card"><span class="public-service-icon public-update-icon">◷</span><h3>Stay up to date</h3><p>Find community announcements and see updates on your own requests and reports.</p></article>
                </div>
            </section>
        </main>

        <footer class="public-footer"><span>Barangay Estanza · Community Services</span><span>We’re here to help our community.</span></footer>
    </div>
</body>
</html>
