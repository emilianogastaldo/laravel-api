@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <header class="mt-5 d-flex aligh-items-center justify-content-between">
        <h1>Projects</h1>
      
        {{-- Filtro --}}
        <form action="{{route('admin.projects.index')}}" method="GET">
          <div class="input-group">
            <select class="form-select" name="published-filter">
              <option value="">Stato</option>
              <option value="pubblico" @if ($publishedFilter === 'pubblico') selected @endif>Pubblici</option>
              <option value="bozza" @if ($publishedFilter === 'bozza') selected @endif>Non pubblici</option>
            </select>
            <select class="form-select" name="type-filter">
              <option value="">Scegli tipo</option>
              @foreach ( $types as $type)
              {{-- Devo mettere == perché uno è una stringa dal form l'altro un numero dal database!! --}}
              <option value="{{$type->id}}" @if ($typeFilter == $type->id) selected @endif>{{$type->label}}</option>
              @endforeach              
            </select>
            <select class="form-select" name="tech-filter">
              <option value="">Scegli tecnologia</option>
              @foreach ( $techs as $tech)
              {{-- Devo mettere == perché uno è una stringa dal form l'altro un numero dal database!! --}}
              <option value="{{$tech->id}}" @if ($techFilter == $tech->id) selected @endif>{{$tech->label}}</option>
              @endforeach              
            </select>
            <button class="btn btn-info" type="submit">Button</button>
            <a href="{{route('admin.projects.index')}}" class="btn btn-outline-warning" type="reset">Reset</a>
          </div>
        </form>
    </header>
    <table class="table table-sm border-top">
        <thead>
          <tr class="align-middle">
            <th scope="col">Id</th>
            <th scope="col">Title</th>
            <th scope="col">Autore</th>
            {{-- <th scope="col">Slug</th> --}}
            <th scope="col">Tipo</th>
            <th scope="col">Tecnologia</th>
            <th scope="col">Stato</th>
            <th scope="col">Creato il</th>
            <th scope="col">Ultima modifica</th>
            <th scope="col">
              <div class="d-flex flex-column justify-content-center gap-3">
                <a href="{{route('admin.projects.create')}}" class="btn btn-success"><i class="fas fa-plus-square "></i> Nuovo</a>
                <a href="{{route('admin.projects.trash')}}" class="btn btn-secondary"><i class="fas fa-trash-can "></i> Cestino</a>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
            @forelse ($projects as $project )
            <tr>
              <th scope="row">{{$project->id}}</th>
              <td>{{$project->title}}</td>
              <td>{{$project->user ? $project->user->name : 'Anonimo'}}</td>
              {{-- <td>{{$project->slug}}</td> --}}
              <td>
                @if ($project->type)
                  <span class="badge" style="background-color: {{$project->type->color}}">{{$project->type->label}}</span>                    
                @else
                  <span>Nessuna</span>
                @endif
              </td>
              <td>
                @forelse ( $project->technologies as $tech )
                <span class="badge rounded-pill text-bg-{{$tech->color}}">{{$tech->label}}</span>               
                @empty                  
                <span>Nessuna</span>
                @endforelse
              </td>
              <td>
                <form action="{{route('admin.projects.publish', $project)}}" method="POST" class="publication-form" onclick = "this.submit()">
                  @csrf
                  @method('PATCH')
                  <div class="form-check form-switch">
                    <input role="button" class="form-check-input" type="checkbox" role="switch" id="is_published-{{$project->id}}" @if ($project->is_published) checked @endif >
                    <label class="form-check-label" for="is_published-{{$project->id}}">{{$project->is_published ? 'Pubblicato' : 'Non pubblicato'}}</label>
                  </div>                
                </form>
              </td>
              <td>{{$project->getFormatedDate('created_at')}}</td>
              <td>{{$project->getFormatedDate('updated_at', 'd-m-Y H:i:s')}}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{route('admin.projects.show', $project)}}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                    {{-- Solo l'utente corretto può modificare --}}
                    {{-- @if(Auth::id() === $project->user_id) --}}
                    <a href="{{route('admin.projects.edit', $project)}}" class="btn btn-outline-warning"><i class="fas fa-pen"></i></a>
                    <form action="{{route('admin.projects.destroy', $project)}}" method="POST" class="delete-form" data-bs-toggle="modal" data-bs-target="#modal">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger"><i class="fas fa-trash-can"></i></button>
                    </form>
                    {{-- @endif --}}
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

      {{-- Paginazione --}}
      @if ($projects->hasPages())
          {{$projects->links()}}
      @endif  
@endsection

@section('scripts')
    @vite('resources/js/delete_confirmation.js')
    {{-- Invece di fare tutto questo script posso usare un onclick = "this.submit()" nel form --}}
    {{-- <script>
      const publicationForms = document.querySelectorAll('.publication-form');
      publicationForms.forEach( form => {
        form.addEventListener('click', () =>{
          form.submit();
        })
      });
    </script> --}}
@endsection