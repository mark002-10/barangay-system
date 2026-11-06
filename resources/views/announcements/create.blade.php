@extends('layouts.portal')
@section('title', 'New announcement')
@section('content')
<div class="module-heading"><div><p class="eyebrow">ANNOUNCEMENTS <span>/</span> NEW</p><h1>New announcement</h1><p class="heading-copy">Save a draft or publish it to the dashboard immediately.</p></div><a class="secondary-action" href="{{ route('announcements.index') }}">Back to announcements</a></div>
<section class="panel form-panel"><form method="POST" action="{{ route('announcements.store') }}">@csrf @include('announcements._form', ['announcement' => $announcement, 'submitLabel' => 'Save announcement'])</form></section>
@endsection
