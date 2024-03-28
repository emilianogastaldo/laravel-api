<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Models\Type;
use Illuminate\Support\Arr;
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
        $filter = $request->query('filter');

        $query = Project::orderByDesc('updated_at')->orderByDesc('created_at');

        if ($filter) {
            $value = $filter === 'pubblico';
            $query->whereIsPublished($value);
        }

        $projects = $query->paginate(10)->withQueryString();
        return view('admin.projects.index', compact('projects', 'filter'));
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
                'technologies' => 'nullable|exists:technologies,id'
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
                'technologies.exists' => 'Tecnologia scelta non valida'
            ]
        );
        // Recupero i dati dopo averli validati
        $data = $request->all();
        // creoun nuovo Project e lo riempio
        $new_project = new Project();
        $new_project['slug'] = Str::slug($data['title']);
        if (Arr::exists($data, 'image')) {
            $extension = $data['image']->extension();
            $img_url = Storage::putFileAs('project_images', $data['image'], "{$new_project['slug']}.$extension");
            $new_project['image'] = $img_url;
        }
        $new_project->fill($data);

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
                'technologies' => 'nullable|exists:technologies,id'
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
                'technologies.exists' => 'Tecnologia scelta non valida'
            ]
        );
        $data = $request->all();
        $project['slug'] = Str::slug($data['title']);

        if (Arr::exists($data, 'image')) {
            if ($project->image) Storage::delete($project->image);

            $extension = $data['image']->extension();
            $img_url = Storage::putFileAs('project_images', $data['image'], "$project->slug.$extension");
            $project->image = $img_url;
        }

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
        if ($project->image) Storage::delete($project->image);
        if ($project->has('technologies')) $project->technologies()->detach();
        $project->forceDelete();
        return to_route('admin.projects.index')->with('type', 'danger')->with('message', 'Progetto eliminato con successo');
    }


    // ROTTE SOFT DELETE

    public function trash()
    {
        $project = Project::onlyTrashed()->get();
        return view('admin.projects.trash', compact('project'));
    }

    public function restore(Project $project)
    {
        $project->restore();
        return to_route('admin.projects.index')->with('type', 'success')->with('message', 'Post ripristinato correttamente');
    }

    public function drop(Project $project)
    {
        if ($project->image) Storage::delete($project->image);
        $project->forceDelete();
        return to_route('admin.projects.trash')->with('type', 'warning')->with('message', 'Post eliminato definitivamente');
    }
}
