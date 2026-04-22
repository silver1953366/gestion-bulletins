<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatMatiere;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Semestre; // Ajouté pour les filtres
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatMatiereController extends Controller
{
    /**
     * Interface principale des résultats par matière
     */
    public function index(Request $request)
    {
        // Données pour les menus de filtrage
        $classes = Classe::all();
        $matieres = Matiere::all();
        $semestres = Semestre::all();

        // Requête de base avec chargement des relations pour éviter les requêtes N+1
        $query = ResultatMatiere::with(['etudiant.user', 'matiere']);

        // Filtres dynamiques
        if ($request->filled('classe_id')) {
            $query->whereHas('etudiant.inscriptions', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }

        // Pagination pour garder une interface fluide
        $resultats = $query->latest()->paginate(15);

        return view('admin.resultats.matieres', compact('resultats', 'classes', 'matieres', 'semestres'));
    }

    /**
     * Calcul de la moyenne d'un étudiant selon les règles LP ASUR
     */
    public function calculerMoyenne($etudiantId, $matiereId)
    {
        $notes = Note::where('etudiant_id', $etudiantId)
                     ->where('matiere_id', $matiereId)
                     ->get();

        if ($notes->isEmpty()) return null;

        // 1. RÈGLE DU RATTRAPAGE : Si une note de rattrapage existe, elle remplace tout 
        $noteRattrapage = $notes->where('type', 'RATTRAPAGE')->first();
        
        if ($noteRattrapage) {
            $moyenneBase = $noteRattrapage->valeur;
            $rattrapageUtilise = true;
        } else {
            // 2. RÈGLE DE LA NOTE UNIQUE OU FORMULE STANDARD
            if ($notes->count() === 1) {
                $moyenneBase = $notes->first()->valeur;
            } else {
                // FORMULE : (CC * 40%) + (EXAM * 60%)
                $moyenneCC = $notes->where('type', 'CC')->avg('valeur') ?? 0;
                $noteExamen = $notes->where('type', 'EXAMEN')->first()?->valeur ?? 0;
                $moyenneBase = ($moyenneCC * 0.4) + ($noteExamen * 0.6);
            }
            $rattrapageUtilise = false;
        }

        // 3. PÉNALITÉ D'ABSENCES : -0.01 pt par heure 
        $totalHeuresAbsence = Absence::where('etudiant_id', $etudiantId)
                                     ->where('matiere_id', $matiereId)
                                     ->sum('heures');
        
        $penalite = $totalHeuresAbsence * 0.01;
        $moyenneFinale = max(0, $moyenneBase - $penalite);

        // Sauvegarde ou Mise à jour
        $resultat = ResultatMatiere::updateOrCreate(
            ['etudiant_id' => $etudiantId, 'matiere_id' => $matiereId],
            [
                'moyenne' => $moyenneFinale,
                'utilise_rattrapage' => $rattrapageUtilise
            ]
        );

        Log::info("Moyenne calculée", [
            'etudiant' => $etudiantId,
            'matiere' => $matiereId,
            'score' => $moyenneFinale
        ]);

        return $resultat;
    }

    /**
     * Calcul en mode BATCH pour une promotion
     */
    public function calculerPourClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
        ]);

        $etudiants = Etudiant::whereHas('inscriptions', function($q) use ($request) {
            $q->where('classe_id', $request->classe_id);
        })->get();

        if($etudiants->isEmpty()) {
            return back()->with('error', 'Aucun étudiant trouvé dans cette classe.');
        }

        foreach ($etudiants as $etudiant) {
            $this->calculerMoyenne($etudiant->id, $request->matiere_id);
        }

        return back()->with('success', 'Calcul terminé pour ' . $etudiants->count() . ' étudiants.');
    }
}