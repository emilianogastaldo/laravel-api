@extends('layouts.app')

@section('title', 'Nuovo Progetto')

@section('content')
<h1>Crea un nuovo progetto</h1>
@include('includes.projects.form')
@endsection

@section('scripts')
@vite('resources/js/image_preview.js')
@vite('resources/js/slug_preview.js')
@endsection