<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Create account · Barangay Estanza</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page auth-register-page">
    <main class="auth-shell auth-register-shell">
        <section class="auth-card" aria-labelledby="auth-title">
            <a class="auth-brand" href="/" aria-label="Barangay Estanza home"><span class="auth-seal">BE</span><span>Barangay Estanza<small>Community services portal</small></span></a>
            <div class="auth-heading"><p class="auth-eyebrow">RESIDENT ACCOUNT</p><h1 id="auth-title">Create your account</h1><p>Enter your information once. An administrator will review it before you can use the portal.</p></div>
            <form class="auth-form" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="auth-section-heading">Account sign-in</div>
                <div class="auth-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <div class="auth-field"><label for="password">Password</label><div class="password-input-wrap"><input id="password" name="password" type="password" autocomplete="new-password" minlength="12" data-password-toggle required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle-button><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div><small class="auth-hint">Use at least 12 characters.</small>@error('password')<small class="auth-error">{{ $message }}</small>@enderror</div>
                <div class="auth-field"><label for="password_confirmation">Confirm password</label><div class="password-input-wrap"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" data-password-toggle required><button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" data-password-toggle-button><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></div></div>
                <hr class="auth-divider">
                <div class="auth-section-heading auth-section-spaced">Resident information</div>
                <div class="auth-profile-grid">
                    <div class="auth-field"><label for="first_name">First name</label><input id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" data-name-only required>@error('first_name')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="middle_name">Middle name</label><input id="middle_name" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" data-name-only required>@error('middle_name')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="last_name">Last name</label><input id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" data-name-only required>@error('last_name')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="suffix">Suffix</label><input id="suffix" name="suffix" value="{{ old('suffix') }}" placeholder="Jr., Sr., III" required>@error('suffix')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="birth_date">Birth date</label><input id="birth_date" name="birth_date" type="date" max="{{ today()->subDay()->toDateString() }}" value="{{ old('birth_date') }}" autocomplete="bday" required>@error('birth_date')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="phone">Phone number</label><input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" title="Enter 7 to 15 digits only, without spaces or symbols." value="{{ old('phone') }}" autocomplete="tel" data-digits-only required>@error('phone')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="gender">Gender</label><select id="gender" name="gender" required><option value="">Select one</option>@foreach ($genders as $gender)<option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>@endforeach</select>@error('gender')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field"><label for="civil_status">Civil status</label><select id="civil_status" name="civil_status" required><option value="">Select one</option>@foreach ($civilStatuses as $civilStatus)<option value="{{ $civilStatus }}" @selected(old('civil_status') === $civilStatus)>{{ $civilStatus }}</option>@endforeach</select>@error('civil_status')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field auth-field-span"><label for="purok">Purok</label><input id="purok" name="purok" value="{{ old('purok') }}" autocomplete="address-level3" required>@error('purok')<small class="auth-error">{{ $message }}</small>@enderror</div>
                    <div class="auth-field auth-field-span"><label for="address">House number, street, and barangay</label><textarea id="address" name="address" rows="2" autocomplete="street-address" required>{{ old('address') }}</textarea>@error('address')<small class="auth-error">{{ $message }}</small>@enderror</div>
                </div>
                <hr class="auth-divider">
                <div class="auth-notice auth-privacy-note"><strong>How we use your information</strong><br>We use the details you provide to review your resident account and handle your service requests. Authorized barangay staff and administrators can view this information. Please contact the barangay office if you need to correct your details.</div>
                <button class="auth-submit" type="submit">Submit for verification</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </section>
        <p class="auth-footer">Barangay Estanza · Community services portal</p>
    </main>
</body>
</html>
