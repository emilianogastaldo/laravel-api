@extends('layouts.app')

@section('title', 'Modifica Progetto')

@section('content')
<h1>Modifica il progetto</h1>
@include('includes.projects.form')
@endsection

@section('scripts')
@vite('resources/js/image_preview.js')
@vite('resources/js/slug_preview.js')
@endsection