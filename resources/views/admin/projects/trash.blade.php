@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header class="mt-5 d-flex aligh-items-center justify-content-between">
        <h1>Projects eliminati</h1>
    </header>
    <hr>
    <section>
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Tenologia</th>
                    <th scope="col">Stato</th>
                    <th scope="col">Creato il</th>
                    <th scope="col">Ultima modifica</th>
                    <th scope="col">
                        <div class="text-center">
                            <a href="#" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Svuota cestino</a>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project )
                <tr>
                    <th scope="row">{{$project->id}}</th>
                    <td>{{$project->title}}</td>
                    <td>{{$project->slug}}</td>
                    <td>
                        @if ($project->type)
                        <span class="badge" style="background-color: {{$project->type->color}}">{{$project->type->label}}</span>                    
                        @else
                        <span>Nessuna</span>
                        @endif
                    </td>
                    <td>
                        @forelse ( $project->technologies as $tech )
                        <span class="badge rounded-pill text-bg-{{$tech->color}}" >{{$tech->label}}</span>               
                        @empty                  
                        <span>Nessuna</span>
                        @endforelse
                    </td>
                    <td>
                        {{$project->is_published ? 'Pubblicato' : 'Non pubblicato'}}
                    </td>
                    <td>{{$project->getFormatedDate('created_at')}}</td>
                    <td>{{$project->getFormatedDate('updated_at', 'd-m-Y H:i:s')}}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{route('admin.projects.show', $project)}}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="{{route('admin.projects.edit', $project)}}" class="btn btn-outline-warning"><i class="fas fa-pen"></i></a>
                            <form action="{{route('admin.projects.drop', $project)}}" method="POST" class="delete-form" data-bs-toggle="modal" data-bs-target="#modal">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash-can"></i></button>
                            </form>
                            <form action="{{route('admin.projects.restore', $project)}}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-success"><i class="fas fa-rotate"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                
                @empty
                <tr>
                    <td colspan="10"><h3>Non ci sono progetti</h3></td>
                </tr>
                @endforelse
                
            </tbody>
        </table>
    </section>
    <footer>
        <a href="{{route('admin.projects.index')}}" class="btn btn-outline-secondary"><i class="far fa-hand-point-left me-2"></i>Torna indietro</a>
    </footer>

      {{-- Paginazione --}}
      {{-- @if ($projects->hasPages())
          {{$projects->links()}}
      @endif   --}}
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
@endsection