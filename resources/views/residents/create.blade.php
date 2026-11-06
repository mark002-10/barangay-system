@extends('layouts.portal')
@section('title', 'Add resident')
@section('content')
<div class="module-heading"><div><p class="eyebrow">RESIDENTS <span>/</span> NEW RECORD</p><h1>Add resident</h1><p class="heading-copy">Enter only the information needed for barangay services.</p></div><a class="secondary-action" href="{{ route('residents.index') }}">Back to residents</a></div>
<section class="panel form-panel"><form method="POST" action="{{ route('residents.store') }}">@csrf @include('residents._form', ['resident' => $resident, 'submitLabel' => 'Save resident'])</form></section>
@endsection
