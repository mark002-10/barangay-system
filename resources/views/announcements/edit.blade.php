@extends('layouts.portal')
@section('title', 'Edit announcement')
@section('content')
<div class="module-heading"><div><p class="eyebrow">ANNOUNCEMENTS <span>/</span> EDIT</p><h1>Edit announcement</h1><p class="heading-copy">Update the content or publication status.</p></div><a class="secondary-action" href="{{ route('announcements.index') }}">Back to announcements</a></div>
<section class="panel form-panel"><form method="POST" action="{{ route('announcements.update', $announcement) }}">@csrf @method('PUT') @include('announcements._form', ['announcement' => $announcement, 'submitLabel' => 'Save changes'])</form></section>
@endsection
