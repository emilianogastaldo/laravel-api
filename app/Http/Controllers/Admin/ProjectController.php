<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Models\Type;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $publishedFilter = $request->query('published-filter');
        $typeFilter = $request->query('type-filter');
        $techFilter = $request->query('tech-filter');

        $query = Project::orderByDesc('updated_at')->orderByDesc('created_at');

        if ($publishedFilter) {
            $value = $publishedFilter === 'pubblico';
            $query->whereIsPublished($value);
        }
        if ($typeFilter) $query->whereTypeId($typeFilter);
        if ($techFilter) {
            // Se ho una relazione many to many uso whereHas
            // Dico con chi voglio relazionarmi e nella call back fn gli dico come si deve relazionare
            $query->whereHas('technologies', function ($query) use ($techFilter) {
                // Ricerca nella colonna id della tabella technologies il parametro che ho passato
                $query->where('technologies.id', $techFilter);
            });
        }

        $projects = $query->paginate(10)->withQueryString();
        $types = Type::select('id', 'label')->get();
        $techs = Technology::select('id', 'label')->get();
        return view('admin.projects.index', compact('projects', 'publishedFilter', 'typeFilter', 'techFilter', 'types', 'techs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = new Project();
        $types = Type::select('label', 'id')->get();
        $techs = Technology::select('label', 'id')->get();
        return view('admin.projects.create', compact('project', 'types', 'techs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|min:5|max:50|unique:projects',
                'image' => 'nullable|image|mimes:png,jpg',
                'content' => 'required|string',
                'type_id' => 'nullable|exists:types,id',
                'technologies' => 'nullable|exists:technologies,id',
                'is_published' => 'nullable|boolean'
            ],
            [
                'title.required' => 'Il titolo è obbligatorio',
                'title.min' => 'Il titolo deve avere almeno :min caratteri',
                'title.max' => 'Il titolo deve essere massimo di :max caratteri',
                'title.unique' => 'Non ci possono essere due titoli uguali',
                'image.image' => 'Carica una immagine',
                'image.mimes' => 'Si supportano solo le immagini con estensione .png o .jpg',
                'content.required' => 'La descrizione è obbligatoria',
                'type_id.exists' => 'Categoria non valida',
                'technologies.exists' => 'Tecnologia scelta non valida',
                'is_published' => 'Il calore del campo pubblicazione non è valido'
            ]
        );
        // Recupero i dati dopo averli validati
        $data = $request->all();
        // creoun nuovo Project e lo riempio
        $new_project = new Project();
        if (Arr::exists($data, 'image')) {
            $extension = $data['image']->extension();
            $img_url = Storage::putFileAs('project_images', $data['image'], "{$new_project['slug']}.$extension");
            $new_project['image'] = $img_url;
        }
        $new_project->fill($data);
        $new_project->slug = Str::slug($data['title']);
        $new_project->is_published = Arr::exists($data, 'is_published');

        // Inserico come autore l'utente attualmente loggato
        $new_project->user_id = Auth::id();

        // salvo il progetto
        $new_project->save();

        // creo la realzione tra progetto e tecnologia
        if (Arr::exists($data, 'techs')) $new_project->technologies()->attach($data['techs']);

        return to_route('admin.projects.show', $new_project)->with('message', 'Pogretto creato con successo')->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        // Controllo se è autorizzato a modificare il progetto
        // if ($project->user_id !== Auth::id()) {
        //     return to_route('admin.projects.index')->with('message', 'Non sei autorizzato a modificare questo progetto')->with('type', 'danger');
        // }
        $types = Type::select('label', 'id')->get();
        $techs = Technology::select('label', 'id')->get();
        $old_techs = $project->technologies->pluck('id')->toArray();
        return view('admin.projects.edit', compact('project', 'types', 'techs', 'old_techs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate(
            [
                'title' => ['required', 'string', 'min:5', 'max:50', Rule::unique('projects')->ignore($project->id)],
                'image' => 'nullable|image|mimes:png,jpg',
                'content' => 'required|string',
                'type_id' => 'nullable|exists:types,id',
                'technologies' => 'nullable|exists:technologies,id',
                'is_published' => 'nullable|boolean'
            ],
            [
                'title.required' => 'Il titolo è obbligatorio',
                'title.min' => 'Il titolo deve avere almeno :min caratteri',
                'title.max' => 'Il titolo deve essere massimo di :max caratteri',
                'title.unique' => 'Non ci possono essere due titoli uguali',
                'image.image' => 'Carica una immagine',
                'image.mimes' => 'Si supportano solo le immagini con estensione .png o .jpg',
                'content.required' => 'La descrizione è obbligatoria',
                'type_id' => 'Categoria non valida',
                'technologies.exists' => 'Tecnologia scelta non valida',
                'is_published' => 'Il calore del campo pubblicazione non è valido'
            ]
        );
        $data = $request->all();

        $data['slug'] = Str::slug($data['title']);
        // Assegno vero o falso in base a se arriva o meno la chiave is_published
        $data['is_published'] = Arr::exists($data, 'is_published');

        if (Arr::exists($data, 'image')) {
            // elimino la vecchia immagine dalla cartella
            if ($project->image) Storage::delete($project->image);

            $extension = $data['image']->extension();
            $img_url = Storage::putFileAs('project_images', $data['image'], "$project->slug.$extension");
            $data['image'] = $img_url;
        }
        // Usare update equivale a fare:
        // $project->fill($data);
        // $project->save();
        $project->update($data);

        // Aggiorno il legame tra progetti e tecnologie
        if (Arr::exists($data, 'techs')) $project->technologies()->sync($data['techs']);
        elseif (!Arr::exists($data, 'techs') && $project->has('technologies')) $project->technologies()->detach();

        return to_route('admin.projects.show', $project)->with('message', 'Pogretto modificato con successo')->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // if ($project->image) Storage::delete($project->image);
        // if ($project->has('technologies')) $project->technologies()->detach();
        $project->delete();
        return to_route('admin.projects.index')
            ->with('toast-button-type', 'danger')
            ->with('toast-message', 'Progetto eliminato con successo')
            ->with('toast-label', config('app.name'))
            ->with('toast-method', 'PATCH')
            ->with('toast-route', route('admin.projects.restore', $project->id))
            ->with('toast-button-label', 'Annulla');
    }


    // ROTTE SOFT DELETE

    public function trash(Request $request)
    {
        // Visualizzo solo quelli che hanno qualcosa nella colonna deleted_at
        $filter = $request->query('filter');
        $projects = Project::onlyTrashed()->get();
        // paginate(10)->withQueryString();
        return view('admin.projects.trash', compact('projects', 'filter'));
    }

    public function restore(Project $project)
    {
        $project->restore();
        return to_route('admin.projects.index')->with('type', 'success')->with('message', 'Progetto ripristinato correttamente');
    }

    public function drop(Project $project)
    {
        if ($project->image) Storage::delete($project->image);
        if ($project->has('technologies')) $project->technologies()->detach();
        $project->forceDelete();
        return to_route('admin.projects.trash')->with('type', 'warning')->with('message', 'Progetto eliminato definitivamente');
    }

    // public function dropAll($projects)
    // {
    //     foreach ($projects as $project) {
    //         if ($project->image) Storage::delete($project->image);
    //         if ($project->has('technologies')) $project->technologies()->detach();
    //         $project->forceDelete();
    //     }
    //     return back()->with('type', 'warning')->with('message', 'Progetto eliminato definitivamente');
    // }

    // ROTTA PATCH DELL'INDEX
    public function togglePublication(Project $project)
    {
        // Controllo se è autorizzato a modificare il progetto
        // if ($project->user_id !== Auth::id()) {
        //     return to_route('admin.projects.index')->with('message', 'Non sei autorizzato a modificare questo progetto')->with('type', 'danger');
        // }

        $project->is_published = !$project->is_published;
        $project->save();

        $action = $project->is_published ? 'pubblicato' : 'salvato come bozza';
        $type = $project->is_published ? 'success' : 'info';

        return back()->with('message', "Il progetto $project->title è stato $action")->with('type', $type);
    }
}
