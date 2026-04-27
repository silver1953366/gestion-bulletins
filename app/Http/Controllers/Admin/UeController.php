<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ue;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * UeController
 * * Gère l'architecture des Unités d'Enseignement. 
 * Supporte l'affichage par parcours complet pour éviter les confusions de semestres.
 */
class UeController extends Controller
{
    /**
     * Affiche la liste des UEs avec leur contexte académique complet.
     */
    public function index()
    {
        // 1. On récupère les UEs avec les relations pour afficher "Filière > Niveau > Semestre"
        $ues = Ue::with(['semestre.classe.filiere', 'semestre.classe.niveau'])
            ->withCount('matieres')
            ->orderBy('code')
            ->paginate(12);

        // 2. On récupère les semestres pour les listes déroulantes des modales
        // On les trie par Filière puis par Libellé pour une sélection plus intuitive
        $semestres = Semestre::with(['classe.filiere', 'classe.niveau'])
            ->get()
            ->sortBy(function($semestre) {
                return ($semestre->classe->filiere->nom ?? '') . ($semestre->libelle ?? '');
            });

        return view('admin.ues.index', compact('ues', 'semestres'));
    }

    /**
     * Enregistre une nouvelle UE dans la base de données.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:ues,code',
            'libelle'     => 'required|string|max:255',
            'semestre_id' => 'required|exists:semestres,id',
          
        ]);

        try {
            DB::beginTransaction();
            
            $ue = Ue::create($validated);
            
            DB::commit();

            return redirect()->route('admin.ues.index')
                ->with('success', "L'Unité d'Enseignement [{$ue->code}] a été créée avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création de l'UE : " . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', "Une erreur est survenue lors de la création : " . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations d'une UE existante.
     */
    public function update(Request $request, Ue $ue)
    {
        $validated = $request->validate([
            'code'        => [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('ues', 'code')->ignore($ue->id)
            ],
            'libelle'     => 'required|string|max:255',
            'semestre_id' => 'required|exists:semestres,id',

        ]);

        try {
            $ue->update($validated);

            return redirect()->route('admin.ues.index')
                ->with('success', "L'UE {$ue->code} a été mise à jour.");

        } catch (\Exception $e) {
            Log::error("Erreur modification UE ID {$ue->id} : " . $e->getMessage());
            
            return back()->with('error', "Erreur lors de la modification.");
        }
    }

    /**
     * Supprime une UE si elle ne contient pas de données liées (Matières).
     */
    public function destroy(Ue $ue)
    {
        try {
            // Vérification de l'intégrité (Contrainte métier)
            if ($ue->matieres()->count() > 0) {
                return back()->with('error', "Suppression impossible : Des matières sont encore liées à cette UE.");
            }

            // Vérification si des résultats y sont déjà rattachés
            if ($ue->resultatsUes()->count() > 0) {
                return back()->with('error', "Suppression impossible : Des notes d'étudiants existent pour cette UE.");
            }

            $ue->delete();

            return redirect()->route('admin.ues.index')
                ->with('success', "L'Unité d'Enseignement a été définitivement supprimée.");

        } catch (\Exception $e) {
            Log::error("Erreur suppression UE ID {$ue->id} : " . $e->getMessage());
            return back()->with('error', "Une erreur technique a empêché la suppression.");
        }
    }

    /**
     * Redirections de sécurité (Méthodes non utilisées dans une approche Modale)
     */
    public function create() { return redirect()->route('admin.ues.index'); }
    public function edit(Ue $ue) { return redirect()->route('admin.ues.index'); }
    public function show(Ue $ue) { return redirect()->route('admin.ues.index'); }

    /**
     * API - Retourne les matières d'une UE en JSON (Optionnel pour vos futurs besoins JS)
     */
    public function getMatieres(Ue $ue)
    {
        return response()->json($ue->matieres);
    }
}