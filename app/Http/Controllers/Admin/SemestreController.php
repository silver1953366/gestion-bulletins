<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\Classe;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    public function index()
    {
        // On récupère les semestres avec leur classe parente
        $semestres = Semestre::with('classe')->orderBy('libelle')->paginate(10);
        $classes = Classe::all();

        return view('admin.semestres.index', compact('semestres', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle'   => 'required|string|max:100',
            'classe_id' => 'required|exists:classes,id',
        ]);

        Semestre::create($validated);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre ajouté avec succès.');
    }

    public function update(Request $request, Semestre $semestre)
    {
        $validated = $request->validate([
            'libelle'   => 'required|string|max:100',
            'classe_id' => 'required|exists:classes,id',
        ]);

        $semestre->update($validated);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre mis à jour.');
    }

    public function destroy(Semestre $semestre)
    {
        $semestre->delete();
        return back()->with('success', 'Semestre supprimé.');
    }
}