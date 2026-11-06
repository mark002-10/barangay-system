@extends('layouts.portal')
@section('title', 'Resident details')
@section('content')
<div class="module-heading"><div><p class="eyebrow">RESIDENTS <span>/</span> RECORD</p><h1>{{ $resident->full_name }}</h1><p class="heading-copy">Resident record details.</p></div><div class="action-group"><a class="secondary-action" href="{{ route('residents.index') }}">Back to residents</a><a class="primary-action" href="{{ route('residents.edit', $resident) }}">Edit resident</a></div></div>
<section class="panel form-panel"><dl class="detail-grid">
<div><dt>Birth date</dt><dd>{{ $resident->birth_date?->format('M j, Y') ?? '—' }}</dd></div><div><dt>Gender</dt><dd>{{ $resident->gender ?: '—' }}</dd></div><div><dt>Civil status</dt><dd>{{ $resident->civil_status ?: '—' }}</dd></div><div><dt>Purok</dt><dd>{{ $resident->purok ?: '—' }}</dd></div><div><dt>Phone</dt><dd>{{ $resident->phone ?: '—' }}</dd></div><div class="field-wide"><dt>Address</dt><dd>{{ $resident->address ?: '—' }}</dd></div><div><dt>Record status</dt><dd>{{ $resident->is_active ? 'Active' : 'Inactive' }}</dd></div>
</dl></section>
@endsection
