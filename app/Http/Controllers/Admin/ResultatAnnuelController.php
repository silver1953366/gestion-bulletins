<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatAnnuel;
use App\Models\ResultatSemestre;
use App\Models\ResultatUe;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatAnnuelController extends Controller
{
    /**
     * Calcule la décision annuelle finale (Diplômé, Redouble, etc.)
     */
    public function calculerDecisionAnnuelle($etudiantId, $anneeId)
    {
        // 1. Récupération des deux semestres (S5 et S6)
        $resultatsSemestres = ResultatSemestre::where('etudiant_id', $etudiantId)
            ->where('annee_academique_id', $anneeId)
            ->get();

        if ($resultatsSemestres->count() < 2) {
            return null; // On attend que les deux semestres soient calculés
        }

        // Règle 4.4 : Moyenne annuelle = (Moyenne S5 + Moyenne S6) / 2
        $moyenneAnnuelle = $resultatsSemestres->avg('moyenne');
        $totalCreditsAnnuels = $resultatsSemestres->sum('credits_total');

        // Initialisation de la décision
        $decision = "Redouble la Licence 3";
        
        // Règle 4.7 : Diplomation
        if ($totalCreditsAnnuels >= 60) {
            $decision = "Diplômé(e)";
        } 
        else {
            // Vérification règle spécifique : Reprise de soutenance
            // Si tous les crédits acquis SAUF l'UE de soutenance (code UE6-2 par exemple)
            $ueSoutenance = ResultatUe::where('etudiant_id', $etudiantId)
                ->whereHas('ue', function($q) {
                    $q->where('code', 'LIKE', '%UE6-2%')
                      ->orWhere('libelle', 'LIKE', '%Soutenance%');
                })->first();

            if ($ueSoutenance && !$ueSoutenance->isValide() && ($totalCreditsAnnuels >= (60 - $ueSoutenance->ue->matieres->sum('credit')))) {
                $decision = "Reprise de soutenance";
            }
        }

        // Règle 4.8 : Mentions
        $mention = "NÉANT";
        if ($moyenneAnnuelle >= 16) $mention = "TRES BIEN";
        elseif ($moyenneAnnuelle >= 14) $mention = "BIEN";
        elseif ($moyenneAnnuelle >= 12) $mention = "ASSEZ BIEN";
        elseif ($moyenneAnnuelle >= 10) $mention = "PASSABLE";

        $resultat = ResultatAnnuel::updateOrCreate(
            ['etudiant_id' => $etudiantId, 'annee_academique_id' => $anneeId],
            [
                'moyenne' => $moyenneAnnuelle,
                'decision' => $decision,
                'mention' => $mention
            ]
        );

        Log::info("Décision de Jury Annuel", [
            'admin_id' => Auth::id(),
            'etudiant' => $etudiantId,
            'moyenne' => $moyenneAnnuelle,
            'decision' => $decision
        ]);

        return $resultat;
    }

    /**
     * Traitement par lot pour une promotion entière
     */
    public function calculerPromo(Request $request)
    {
        $request->validate([
            'annee_id' => 'required|exists:annees_academiques,id',
            'classe_id' => 'required|exists:classes,id'
        ]);

        $etudiants = Etudiant::whereHas('inscriptions', function($q) use ($request) {
            $q->where('classe_id', $request->classe_id);
        })->get();

        foreach ($etudiants as $etudiant) {
            $this->calculerDecisionAnnuelle($etudiant->id, $request->annee_id);
        }

        return back()->with('success', "Le jury annuel a été généré pour la promotion.");
    }
}