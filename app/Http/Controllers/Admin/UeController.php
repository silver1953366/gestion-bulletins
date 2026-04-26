<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ue;
use App\Models\Semestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UeController extends Controller
{
    /**
     * Affiche la liste des UEs avec pagination et chargement lié.
     */
    public function index()
    {
        // Eager loading du semestre et compte des matières pour l'affichage
        $ues = Ue::with('semestre')
            ->withCount('matieres')
            ->orderBy('code')
            ->paginate(10);

        $semestres = Semestre::orderBy('libelle')->get();

        return view('admin.ues.index', compact('ues', 'semestres'));
    }

    /**
     * Enregistre une nouvelle Unité d'Enseignement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:ues,code',
            'libelle'     => 'required|string|max:255',
            'semestre_id' => 'required|exists:semestres,id',
            'coefficient' => 'required|integer|min:1',
            'credits'     => 'required|integer|min:0',
        ]);

        try {
            Ue::create($validated);
            return redirect()->route('admin.ues.index')
                ->with('success', "L'UE {$validated['code']} a été créée avec succès.");
        } catch (\Exception $e) {
    dd($e->getMessage(), $e->getTraceAsString()); // Arrête tout et affiche l'erreur
}
    }

    /**
     * Met à jour une UE existante.
     */
    public function update(Request $request, Ue $ue)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:ues,code,' . $ue->id,
            'libelle'     => 'required|string|max:255',
            'semestre_id' => 'required|exists:semestres,id',
            'coefficient' => 'required|integer|min:1',
            'credits'     => 'required|integer|min:0',
        ]);

        try {
            $ue->update($validated);
            return redirect()->route('admin.ues.index')
                ->with('success', "L'UE {$ue->code} a été mise à jour.");
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la modification : " . $e->getMessage());
        }
    }

    /**
     * Supprime une UE.
     */
    public function destroy(Ue $ue)
    {
        try {
            // Vérification si des matières sont liées (sécurité intégrité)
            if ($ue->matieres()->count() > 0) {
                return back()->with('error', "Impossible de supprimer cette UE : des matières y sont rattachées.");
            }

            $ue->delete();
            return back()->with('success', "L'Unité d'Enseignement a été supprimée.");
        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la suppression : " . $e->getMessage());
        }
    }

    /**
     * Redirections pour maintenir l'approche Single Page (Modales)
     */
    public function create() { return redirect()->route('admin.ues.index'); }
    public function edit()   { return redirect()->route('admin.ues.index'); }
    public function show()   { return redirect()->route('admin.ues.index'); }
}