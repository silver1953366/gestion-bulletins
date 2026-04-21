<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes avec leurs relations.
     */
    public function index()
    {
        $classes = Classe::with(['filiere', 'niveau'])->orderBy('nom')->paginate(10);
        $filieres = Filiere::all();
        $niveaux = Niveau::all();

        return view('admin.classes.index', compact('classes', 'filieres', 'niveaux'));
    }

    /**
     * Enregistre une nouvelle classe.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'                 => 'required|string|max:100',
            'filiere_id'          => 'required|exists:filieres,id',
            'niveau_id'           => 'required|exists:niveaux,id',
            'annee_universitaire' => 'required|string|max:20',
        ]);

        Classe::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'La classe a été créée avec succès.');
    }

    /**
     * Met à jour une classe existante.
     */
    public function update(Request $request, Classe $classe)
    {
        $validated = $request->validate([
            'nom'                 => 'required|string|max:100',
            'filiere_id'          => 'required|exists:filieres,id',
            'niveau_id'           => 'required|exists:niveaux,id',
            'annee_universitaire' => 'required|string|max:20',
        ]);

        $classe->update($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Classe mise à jour.');
    }

    /**
     * Supprime une classe.
     */
    public function destroy(Classe $classe)
    {
        $classe->delete();
        return back()->with('success', 'La classe a été supprimée.');
    }
}