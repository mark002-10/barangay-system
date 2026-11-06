@extends('layouts.portal')
@section('title', 'Edit resident')
@section('content')
<div class="module-heading"><div><p class="eyebrow">RESIDENTS <span>/</span> EDIT RECORD</p><h1>Edit resident</h1><p class="heading-copy">Update this resident’s information.</p></div><a class="secondary-action" href="{{ route('residents.index') }}">Back to residents</a></div>
<section class="panel form-panel"><form method="POST" action="{{ route('residents.update', $resident) }}">@csrf @method('PUT') @include('residents._form', ['resident' => $resident, 'submitLabel' => 'Save changes'])</form></section>
@endsection
