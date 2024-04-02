<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::whereIsPublished(true)->with('type', 'technologies')->latest()->paginate(3);
        // Qui avrei un problema con i link delle immagini, che per ora sarebbero realtivi, invece sarebbe meglio aver assoluti
        // O faccio un ciclo ora, oppure vado a creare un Accessor nel modello Project che modifica in automatico l'url.
        foreach ($projects as $project) {
            if ($project->image) $project->image = url('storage/' . $project->image);
        }
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
