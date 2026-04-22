<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences avec les listes pour les modales.
     */
    public function index()
    {
        // On récupère les absences avec les relations pour éviter les requêtes N+1
        $absences = Absence::with(['etudiant', 'matiere', 'createdBy'])
            ->latest()
            ->paginate(10);

        // Données nécessaires pour les menus déroulants dans les modales
        $etudiants = Etudiant::orderBy('nom')->get();
        $matieres = Matiere::orderBy('libelle')->get();

        return view('admin.absences.index', compact('absences', 'etudiants', 'matieres'));
    }

    /**
     * Enregistre une nouvelle absence.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id'   => 'required|exists:etudiants,id',
            'matiere_id'    => 'required|exists:matieres,id',
            'heures'        => 'required|integer|min:1',
            'justification' => 'nullable|string|max:500',
        ]);

        // On ajoute l'ID de l'admin/secrétaire qui fait la saisie
        $validated['created_by'] = Auth::id();

        Absence::create($validated);

        return redirect()->route('admin.absences.index')
            ->with('success', 'L\'absence a été enregistrée avec succès.');
    }

    /**
     * Met à jour une absence existante via la modale.
     */
    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'etudiant_id'   => 'required|exists:etudiants,id',
            'matiere_id'    => 'required|exists:matieres,id',
            'heures'        => 'required|integer|min:1',
            'justification' => 'nullable|string|max:500',
        ]);

        $absence->update($validated);

        return redirect()->route('admin.absences.index')
            ->with('success', 'L\'absence a été mise à jour.');
    }

    /**
     * Supprime une absence.
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();

        return back()->with('success', 'L\'absence a été supprimée du registre.');
    }

    /**
     * Les méthodes create(), show() et edit() sont conservées 
     * mais ne sont pas utilisées dans notre approche "Single Page avec Modales".
     */
    public function create() { return redirect()->route('admin.absences.index'); }
    public function show(Absence $absence) { return redirect()->route('admin.absences.index'); }
    public function edit(Absence $absence) { return redirect()->route('admin.absences.index'); }
}