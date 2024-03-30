<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function show(String $slug)
    {
        $project = Project::whereSlug($slug)->first();
        //  Project $project

        if (!$project) abort(404);
        return view('guest.projects.show', compact('project'));
    }
}
