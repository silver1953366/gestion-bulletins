<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semestre;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Http\Request;

class SemestreController extends Controller
{
    /**
     * Affiche la liste des semestres groupés par Filière et Niveau.
     */
    public function index()
    {
        // On récupère les semestres avec leurs relations pour éviter le problème N+1
        $semestres = Semestre::with(['ues', 'classe.filiere', 'classe.niveau'])
            ->withCount('ues')
            ->get();

        // Groupement par Filière pour une interface claire et mutualisée
        $semestresGroupes = $semestres->groupBy(function($semestre) {
            return $semestre->classe->filiere->nom ?? 'Filière non définie';
        });

        // Données nécessaires pour les formulaires de création/édition
        $filieres = Filiere::orderBy('nom')->get();
        $niveaux = Niveau::orderBy('code')->get();
        $classes = Classe::with(['filiere', 'niveau'])->orderBy('nom')->get();
        
        // Année universitaire par défaut
        $anneeActive = "2025-2026"; 

        return view('admin.semestres.index', compact(
            'semestresGroupes', 
            'filieres', 
            'niveaux', 
            'anneeActive', 
            'classes'
        ));
    }

    /**
     * Enregistre un nouveau programme (Tronc Commun).
     */
    public function store(Request $request)
    {
        // On valide la filière et le niveau plutôt qu'une classe unique pour la mutualisation
        $validated = $request->validate([
            'libelle'             => 'required|string|max:20', 
            'annee_universitaire' => 'required|string|max:20',
            'filiere_id'          => 'required|exists:filieres,id', 
            'niveau_id'           => 'required|exists:niveaux,id',
        ]);

        // LOGIQUE DE MUTUALISATION :
        // On vérifie si ce semestre existe déjà pour ce PARCOURS (Filière + Niveau)
        $exists = Semestre::where('libelle', $validated['libelle'])
            ->where('annee_universitaire', $validated['annee_universitaire'])
            ->whereHas('classe', function($query) use ($validated) {
                $query->where('filiere_id', $validated['filiere_id'])
                      ->where('niveau_id', $validated['niveau_id']);
            })->exists();

        if ($exists) {
            return back()->with('error', 'Ce semestre est déjà configuré pour ce parcours pédagogique.');
        }

        // On récupère la première classe disponible pour ce parcours pour servir de clé étrangère
        $classeRef = Classe::where('filiere_id', $validated['filiere_id'])
                           ->where('niveau_id', $validated['niveau_id'])
                           ->first();

        if (!$classeRef) {
            return back()->with('error', 'Impossible de créer le semestre : aucune classe n\'est enregistrée pour cette filière et ce niveau.');
        }

        // Création liée à la classe de référence (mais valable pour tout le parcours)
        Semestre::create([
            'libelle'             => $validated['libelle'],
            'annee_universitaire' => $validated['annee_universitaire'],
            'classe_id'           => $classeRef->id,
        ]);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Le programme de tronc commun a été initialisé avec succès.');
    }

    /**
     * Met à jour le semestre.
     */
    public function update(Request $request, Semestre $semestre)
    {
        $validated = $request->validate([
            'libelle'             => 'required|string|max:20',
            'annee_universitaire' => 'required|string|max:20',
            'classe_id'           => 'required|exists:classes,id',
        ]);

        $semestre->update($validated);

        return redirect()->route('admin.semestres.index')
            ->with('success', 'Le semestre a été mis à jour.');
    }

    /**
     * Suppression sécurisée.
     */
    public function destroy(Semestre $semestre)
    {
        // On empêche la suppression si des UE sont déjà rattachées au tronc commun
        if ($semestre->ues()->count() > 0) {
            return back()->with('error', 'Action refusée : ce semestre contient des Unités d\'Enseignement (UE).');
        }

        $semestre->delete();

        return back()->with('success', 'Le programme a été supprimé.');
    }
}