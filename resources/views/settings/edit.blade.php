@extends('layouts.portal')
@section('title', 'Settings')
@section('content')
<div class="module-heading">
    <div>
        <p class="eyebrow">WORKSPACE <span>/</span> ACCOUNT</p>
        <h1>Settings</h1>
        <p class="heading-copy">Keep your contact and resident details up to date.</p>
    </div>
</div>

<section class="panel form-panel settings-panel">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        <div class="section-label">Account details</div>

        @if ($user->hasRole('user'))
            <div class="form-grid">
                <label class="field">First name <span>*</span><input name="first_name" value="{{ old('first_name', $user->first_name) }}" maxlength="100" autocomplete="given-name" required>@error('first_name')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Middle name <span>*</span><input name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" maxlength="100" autocomplete="additional-name" required>@error('middle_name')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Last name <span>*</span><input name="last_name" value="{{ old('last_name', $user->last_name) }}" maxlength="100" autocomplete="family-name" required>@error('last_name')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Suffix <span>*</span><input name="suffix" value="{{ old('suffix', $user->suffix) }}" maxlength="20" placeholder="Jr., Sr., III" required>@error('suffix')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Birth date <span>*</span><input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->toDateString()) }}" max="{{ today()->subDay()->toDateString() }}" required>@error('birth_date')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Phone number <span>*</span><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="numeric" pattern="[0-9]{7,15}" maxlength="15" required>@error('phone')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Gender <span>*</span><select name="gender" required><option value="">Select one</option>@foreach (\App\Models\Resident::GENDERS as $gender)<option value="{{ $gender }}" @selected(old('gender', $user->gender) === $gender)>{{ $gender }}</option>@endforeach</select>@error('gender')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Civil status <span>*</span><select name="civil_status" required><option value="">Select one</option>@foreach (\App\Models\Resident::CIVIL_STATUSES as $civilStatus)<option value="{{ $civilStatus }}" @selected(old('civil_status', $user->civil_status) === $civilStatus)>{{ $civilStatus }}</option>@endforeach</select>@error('civil_status')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field">Purok <span>*</span><input name="purok" value="{{ old('purok', $user->purok) }}" autocomplete="address-level3" required>@error('purok')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field field-wide">House number, street, and barangay <span>*</span><textarea name="address" rows="3" autocomplete="street-address" required>{{ old('address', $user->address) }}</textarea>@error('address')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <label class="field field-wide">Email address <span>*</span><input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</label>
                <p class="form-intro field-wide">Changes to your name or birth date will need a quick review by the barangay office. Other resident details will update on your resident record.</p>
            </div>
        @else
            <div class="form-grid">
                <label class="field">Full name <span>*</span><input name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required></label>
                <label class="field">Email address <span>*</span><input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>@error('email')<small class="auth-error">{{ $message }}</small>@enderror</label>
            </div>
        @endif

        <hr class="form-divider">
        <div class="section-label">Change password <small>Leave blank to keep your current password.</small></div>
        <div class="form-grid">
            <label class="field">Current password<input type="password" name="current_password" autocomplete="current-password"></label>
            <label class="field">New password<input type="password" name="password" minlength="12" autocomplete="new-password"></label>
            <label class="field">Confirm new password<input type="password" name="password_confirmation" minlength="12" autocomplete="new-password"></label>
        </div>
        <div class="form-actions"><button class="primary-action" type="submit">Save changes</button></div>
    </form>
</section>
@endsection
