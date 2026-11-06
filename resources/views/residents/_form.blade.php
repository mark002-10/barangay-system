<div class="form-grid">
    <label class="field">First name <span>*</span><input name="first_name" value="{{ old('first_name', $resident->first_name) }}" maxlength="100" required>@error('first_name')<small>{{ $message }}</small>@enderror</label>
    <label class="field">Middle name<input name="middle_name" value="{{ old('middle_name', $resident->middle_name) }}" maxlength="100"></label>
    <label class="field">Last name <span>*</span><input name="last_name" value="{{ old('last_name', $resident->last_name) }}" maxlength="100" required>@error('last_name')<small>{{ $message }}</small>@enderror</label>
    <label class="field">Suffix<input name="suffix" value="{{ old('suffix', $resident->suffix) }}" maxlength="20" placeholder="Jr., Sr., III"></label>
    <label class="field">Birth date<input type="date" name="birth_date" value="{{ old('birth_date', $resident->birth_date?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}"></label>
    <label class="field">Gender<select name="gender"><option value="">Choose…</option>@foreach (\App\Models\Resident::GENDERS as $option)<option value="{{ $option }}" @selected(old('gender', $resident->gender) === $option)>{{ $option }}</option>@endforeach</select></label>
    <label class="field">Civil status<select name="civil_status"><option value="">Choose…</option>@foreach (\App\Models\Resident::CIVIL_STATUSES as $option)<option value="{{ $option }}" @selected(old('civil_status', $resident->civil_status) === $option)>{{ $option }}</option>@endforeach</select></label>
    <label class="field">Purok<input name="purok" value="{{ old('purok', $resident->purok) }}" maxlength="100"></label>
    <label class="field">Phone number<input type="tel" name="phone" value="{{ old('phone', $resident->phone) }}" maxlength="30" autocomplete="tel"></label>
    <label class="field field-wide">Address<textarea name="address" rows="3" maxlength="1000">{{ old('address', $resident->address) }}</textarea></label>
    <label class="publish-option field-wide"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $resident->exists ? $resident->is_active : true))><span><strong>Active resident record</strong><small>Inactive residents will not appear in new request forms.</small></span></label>
</div>
<div class="form-actions"><a class="secondary-action" href="{{ route('residents.index') }}">Cancel</a><button class="primary-action" type="submit">{{ $submitLabel }}</button></div>

