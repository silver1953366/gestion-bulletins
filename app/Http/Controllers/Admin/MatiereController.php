<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Ue;
use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières avec filtres et relations.
     */
    public function index()
    {
        // 1. Liste principale avec Eager Loading pour éviter le problème N+1
        $matieres = Matiere::with(['ue.semestre.classe.filiere', 'ue.semestre.classe.niveau'])
            ->orderBy('libelle')
            ->paginate(15);
        
        // 2. Chargement des UEs pour la sélection dans la modale
        $ues = Ue::with(['semestre.classe.filiere', 'semestre.classe.niveau'])
            ->orderBy('code') 
            ->get();

        // 3. Données pour les filtres de la modale (Cascade)
        // Vérifie si 'nom' existe dans filieres, sinon remplace par 'libelle'
        $filieres = Filiere::orderBy('nom')->get();

        // Correction de l'erreur SQL : On trie par 'code' (L1, L2, etc.)
        $niveaux = Niveau::orderBy('code')->get(); 

        return view('admin.matieres.index', compact('matieres', 'ues', 'filieres', 'niveaux'));
    }

    /**
     * Enregistre une nouvelle matière (ECUE).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:matieres,code',
            'libelle'     => 'required|string|max:255',
            'coefficient' => 'required|integer|min:1',
            'credits'     => 'required|integer|min:1',
            'ue_id'       => 'required|exists:ues,id',
        ]);

        Matiere::create($validated);

        return redirect()->route('admin.matieres.index')
            ->with('success', 'La matière a été ajoutée avec succès.');
    }

    /**
     * Met à jour une matière existante.
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:matieres,code,' . $matiere->id,
            'libelle'     => 'required|string|max:255',
            'coefficient' => 'required|integer|min:1',
            'credits'     => 'required|integer|min:1',
            'ue_id'       => 'required|exists:ues,id',
        ]);

        $matiere->update($validated);

        return redirect()->route('admin.matieres.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Supprime une matière.
     */
    public function destroy(Matiere $matiere)
    {
        $matiere->delete();
        return back()->with('success', 'Matière supprimée.');
    }
}