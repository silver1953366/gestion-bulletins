<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Departement;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function index()
    {
    // On ajoute withCount('classes') pour la sécurité de suppression
        $filieres = Filiere::with('departement')->withCount('classes')->orderBy('nom')->paginate(10);
        $departements = Departement::orderBy('nom')->get();

     return view('admin.filieres.index', compact('filieres', 'departements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:255|unique:filieres,nom',
            'departement_id' => 'required|exists:departements,id',
        ]);

        Filiere::create($validated);

        return redirect()->route('admin.filieres.index')
            ->with('success', 'La filière a été créée avec succès.');
    }

    public function update(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:255|unique:filieres,nom,' . $filiere->id,
            'departement_id' => 'required|exists:departements,id',
        ]);

        $filiere->update($validated);

        return redirect()->route('admin.filieres.index')
            ->with('success', 'Filière mise à jour.');
    }

    public function destroy(Filiere $filiere)
    {
        // Attention : Laravel empêchera la suppression si des classes y sont liées 
        // à cause des contraintes d'intégrité de ta migration
        $filiere->delete();
        return back()->with('success', 'Filière supprimée.');
    }
}