@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header class="mt-3">
        <h1>{{$project->title}}</h1>
        <div class="d-flex gap-4">
            <div>
                Tipologia: 
                @if ($project->type)
                <span class="badge" style="background-color: {{$project->type->color}}">{{$project->type->label}}</span>            
                @else
                Nessuna
                @endif
            </div>
            <div>
                Tecnologie: 
                @forelse ( $project->technologies as $tech )
                <span class="badge rounded-pill text-bg-{{$tech->color}}" >{{$tech->label}}</span>               
                @empty                  
                <span>Nessuna</span>
                @endforelse
            </div>
        </div>
    </header>
    <hr>
    <section class="py-3">
        <div class="clearfix">
            @if ($project->image)
            <img src="{{$project->printImage()}}" alt="{{$project->post}}" class="me-4 float-start img-fluid">
            @endif
            <p>{{$project->content}}</p>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p><strong>Creato il: </strong>{{$project->getFormatedDate('created_at', 'd-m-Y H:i:s')}}</p>
                    <p><strong>Ultima modifica il: </strong>{{$project->getFormatedDate('updated_at', 'd-m-Y H:i:s')}}</p>
                </div>
                <p>Autore: <strong>{{$project->user ? $project->user->name : 'Anonimo'}}</strong></p>
            </div>
        </div>
    </section>
    <footer class="d-flex justify-content-between alig-items-center">
        <a href="{{route('guest.home')}}" class="btn btn-outline-secondary"><i class="far fa-hand-point-left me-2"></i>Torna indietro</a>
    </footer>
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection