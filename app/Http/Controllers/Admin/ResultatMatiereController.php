<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatMatiere;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatMatiereController extends Controller
{
    /**
     * Calcul de la moyenne d'un étudiant selon les règles LP ASUR [cite: 51, 53, 57, 86]
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
            // 2. RÈGLE DE LA NOTE UNIQUE : 
            if ($notes->count() === 1) {
                $moyenneBase = $notes->first()->valeur;
            } else {
                // 3. FORMULE STANDARD : (CC * 40%) + (EXAM * 60%) [cite: 51]
                $moyenneCC = $notes->where('type', 'CC')->avg('valeur') ?? 0;
                $noteExamen = $notes->where('type', 'EXAMEN')->first()?->valeur ?? 0;
                $moyenneBase = ($moyenneCC * 0.4) + ($noteExamen * 0.6);
            }
            $rattrapageUtilise = false;
        }

        // 4. PÉNALITÉ D'ABSENCES : -0.01 pt par heure d'absence 
        $totalHeuresAbsence = Absence::where('etudiant_id', $etudiantId)
                                     ->where('matiere_id', $matiereId)
                                     ->sum('heures');
        $penalite = $totalHeuresAbsence * 0.01;
        $moyenneFinale = max(0, $moyenneBase - $penalite);

        $resultat = ResultatMatiere::updateOrCreate(
            ['etudiant_id' => $etudiantId, 'matiere_id' => $matiereId],
            [
                'moyenne' => $moyenneFinale,
                'utilise_rattrapage' => $rattrapageUtilise
            ]
        );

        // 5. JOURNALISATION (Audit Log) 
        Log::info("Calcul moyenne matière exécuté", [
            'admin_id' => Auth::id(),
            'etudiant_id' => $etudiantId,
            'matiere_id' => $matiereId,
            'moyenne_resultat' => $moyenneFinale,
            'absences_deduites' => $totalHeuresAbsence,
            'timestamp' => now()
        ]);

        return $resultat;
    }

    /**
     * Calcul en mode BATCH pour une promotion (Performance < 2s) 
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

        foreach ($etudiants as $etudiant) {
            $this->calculerMoyenne($etudiant->id, $request->matiere_id);
        }

        return back()->with('success', 'Calcul batch terminé pour ' . $etudiants->count() . ' étudiants.');
    }
}