<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\Classe;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    /**
     * Affiche la liste des semestres.
     */
    public function index()
    {
        // On charge la relation 'classe' pour éviter les requêtes N+1
        $semestres = Semestre::with('classe')
            ->orderBy('libelle')
            ->paginate(15);

        $classes = Classe::orderBy('nom')->get();

        return view('admin.semestres.index', compact('semestres', 'classes'));
    }

    /**
     * Enregistre un nouveau semestre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Limité à 10 caractères pour correspondre à la migration string('libelle', 10)
            'libelle'   => 'required|string|max:10', 
            'classe_id' => 'required|exists:classes,id',
        ]);

        Semestre::create($validated);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre ajouté avec succès.');
    }

    /**
     * Met à jour un semestre existant.
     */
    public function update(Request $request, Semestre $semestre)
    {
        $validated = $request->validate([
            'libelle'   => 'required|string|max:100', // On garde une marge ici, mais la DB tronquera à 10 si non modifié
            'classe_id' => 'required|exists:classes,id',
        ]);

        // Correction forcée à 10 pour la sécurité avant l'update
        $validated['libelle'] = substr($validated['libelle'], 0, 10);

        $semestre->update($validated);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Semestre mis à jour avec succès.');
    }

    /**
     * Supprime un semestre.
     */
    public function destroy(Semestre $semestre)
    {
        // Vérification de sécurité pour l'intégrité référentielle
        if($semestre->ues()->count() > 0) {
            return back()->with('error', 'Action impossible : des Unités d\'Enseignement (UE) sont liées à ce semestre.');
        }

        $semestre->delete();

        return back()->with('success', 'Le semestre a été supprimé.');
    }
}