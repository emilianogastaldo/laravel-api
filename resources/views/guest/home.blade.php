@extends('layouts.app')
@section('title', 'Home')
@section('content')
<header>
    <h1>Ecco i miei progetti</h1>
</header>
@if ($projects->hasPages())
    {{$projects->links()}}
@endif 
@forelse ($projects as $project)
<div class="card my-5">
    {{-- @dd($project->slug) --}}
    <div class="card-header d-flex justify-content-between">
        <h3>{{$project->title}}</h3>
        {{-- Mi passo lo slug che laravel metterà in automatico --}}
        <a href="{{route('guest.projects.show', $project->slug)}}" class="btn btn-outline-info">Vedi</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col clearfix">
                @if ($project->image)                
                <img src="{{$project->printImage()}}" alt="{{$project->title}}" class="float-start me-3">                   
                @endif
                <h4 class="card-title">{{$project->title}}</h4>
                <h6 class="card-subtitle mb-2 text-body-secondary mt-3">Creato il: {{$project->getFormatedDate('created_at')}}</h6>
                <p class="card-text">{{$project->content}}</p>
            </div>
        </div>
    </div>
  </div>    
@empty
    <h3 class="text-center">Non ci sono progetti</h3>
@endforelse


@endsection