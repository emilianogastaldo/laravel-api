<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeProjectController extends Controller
{
    public function __invoke(string $slug)
    {
        $type = Type::whereSlug($slug)->first();
        if (!$type) return response(null, 404);
        // Se invio giù questi progetti sono senza la relazione con le tecnologie, 
        // $projects = $type->projects;
        // Per inserire la relazione devo usare PRIMA il metodo load()
        // $type->load('projects.user', 'projects.technologies', 'projects.type');
        // Con questo where mi da un oggetto e non un array, quindi bisogna cambiare approccio
        // $projects = $type->projects->where('is_published', 1);

        // Riscrivo per avere un array come risultato
        $projects = Project::whereTypeId($type->id)->whereIsPublished(true)->with('user', 'technologies', 'type')->get();

        return response()->json(['projects' => $projects, 'label' => $type->label]);
    }
}
