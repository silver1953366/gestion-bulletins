<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Ue;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières et les UEs pour les modales.
     */
    public function index()
    {
        // On récupère les matières avec leur UE parente (Eager Loading)
        $matieres = Matiere::with('ue')->orderBy('libelle')->paginate(10);
        
        // Liste des UEs pour les menus déroulants (select) des modales
        $ues = Ue::orderBy('libelle')->get();

        return view('admin.matieres.index', compact('matieres', 'ues'));
    }

    /**
     * Enregistre une nouvelle matière.
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
            ->with('success', 'La matière a été ajoutée au programme.');
    }

    /**
     * Met à jour une matière existante.
     */
    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            // On ignore l'ID actuel pour la règle unique du code
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
        // Eloquent gérera la vérification des contraintes si nécessaire
        $matiere->delete();

        return back()->with('success', 'La matière a été retirée du programme.');
    }

    /* | Les méthodes suivantes ne sont pas utilisées dans votre système de modales,
    | mais elles sont conservées pour respecter la structure Resource si besoin.
    */
    public function create() { return redirect()->route('admin.matieres.index'); }
    public function show(Matiere $matiere) { return redirect()->route('admin.matieres.index'); }
    public function edit(Matiere $matiere) { return redirect()->route('admin.matieres.index'); }
}