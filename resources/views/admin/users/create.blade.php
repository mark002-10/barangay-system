@extends('layouts.portal')
@section('title', 'Add account')
@section('content')
<div class="module-heading"><div><p class="eyebrow">ADMINISTRATION <span>/</span> ACCESS</p><h1>Add account</h1><p class="heading-copy">Set the new account’s access level.</p></div></div>
<section class="panel form-panel"><form method="POST" action="{{ route('admin.users.store') }}">@csrf
<div class="form-grid"><label class="field">Full name <span>*</span><input name="name" value="{{ old('name') }}" required maxlength="255"></label><label class="field">Email address <span>*</span><input type="email" name="email" value="{{ old('email') }}" required></label><label class="field">Role <span>*</span><select name="role" required>@foreach ($roles as $role)<option value="{{ $role }}" @selected(old('role', 'staff') === $role)>{{ ucfirst($role) }}</option>@endforeach</select></label><label class="field">Temporary password <span>*</span><input type="password" name="password" minlength="12" required autocomplete="new-password"></label><label class="field">Confirm password <span>*</span><input type="password" name="password_confirmation" minlength="12" required autocomplete="new-password"></label></div>
<div class="form-actions"><a class="secondary-action" href="{{ route('admin.users.index') }}">Cancel</a><button class="primary-action" type="submit">Create account</button></div></form></section>
@endsection
