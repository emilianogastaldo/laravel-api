<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeProjectController extends Controller
{
    public function __invoke(string $id)
    {
        $type = Type::find($id);
        if (!$type) return response(null, 404);
        // Se invio giù questi progetti sono senza la relazione con le tecnologie, 
        // $projects = $type->projects;
        // Per inserire la relazione devo usare PRIMA il metodo load()
        $type->load('projects.user', 'projects.technology', 'projects.type');
        $projects = $type->projects;

        return response()->json(['projects' => $projects, 'label' => $type->label]);
    }
}
