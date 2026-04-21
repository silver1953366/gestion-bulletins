<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatSemestre;
use App\Models\ResultatUe;
use App\Models\Semestre;
use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatSemestreController extends Controller
{
    /**
     * Calcule la moyenne générale du semestre et applique la compensation.
     * Conforme aux règles 4.3, 4.5 et 4.6 du cahier des charges. [cite: 60, 64, 70]
     */
    public function calculerSemestre($etudiantId, $semestreId, $anneeId)
    {
        // Correction Ligne 42 : Syntaxe plus explicite pour éviter l'erreur de token
        $resultatsUe = ResultatUe::where('etudiant_id', $etudiantId)
            ->whereHas('ue', function($query) use ($semestreId) {
                $query->where('semestre_id', $semestreId);
            })
            ->get();

        if ($resultatsUe->isEmpty()) {
            return null;
        }

        $totalPoints = 0;
        $totalCoeff = 0;
        $creditsAcquis = 0;

        // 1. Calcul de la moyenne pondérée du semestre [cite: 61]
        foreach ($resultatsUe as $resUe) {
            $coeffUe = $resUe->ue->matieres->sum('credit'); 
            $totalPoints += ($resUe->moyenne * $coeffUe);
            $totalCoeff += $coeffUe;
        }

        $moyenneSemestre = $totalCoeff > 0 ? $totalPoints / $totalCoeff : 0;
        
        // Règle 4.6 : Le semestre est validé si moyenne >= 10 (donnant accès aux 30 crédits) 
        $semestreValide = ($moyenneSemestre >= 10);

        // 2. Application de la compensation et cumul des crédits [cite: 68, 69]
        foreach ($resultatsUe as $resUe) {
            $totalCreditsUe = $resUe->ue->matieres->sum('credit');
            
            // Une UE est acquise si Moyenne UE >= 10 OU (Moyenne Semestre >= 10 ET Moyenne UE >= 8)
            if ($resUe->moyenne >= 10 || ($semestreValide && $resUe->moyenne >= 8)) {
                $creditsAcquis += $totalCreditsUe;
                
                if ($resUe->moyenne < 10) {
                    $resUe->update([
                        'compense' => true, 
                        'credits_acquis' => $totalCreditsUe
                    ]);
                }
            }
        }

        $resultat = ResultatSemestre::updateOrCreate(
            [
                'etudiant_id' => $etudiantId, 
                'semestre_id' => $semestreId,
                'annee_academique_id' => $anneeId
            ],
            [
                'moyenne' => $moyenneSemestre,
                'credits_total' => $creditsAcquis,
                'valide' => ($creditsAcquis >= 30) // Validation finale S5 ou S6 
            ]
        );

        // Journalisation obligatoire (Exigence 8.2) [cite: 197]
        Log::info("Délibération Semestre exécutée", [
            'admin_id' => Auth::id(),
            'etudiant' => $etudiantId,
            'statut' => $resultat->valide ? 'ADMIS' : 'AJOURNÉ',
            'timestamp' => now()
        ]);

        return $resultat;
    }

    /**
     * Calcul batch pour une promotion (Performance < 2s) [cite: 202]
     */
    public function calculerClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'semestre_id' => 'required|exists:semestres,id',
            'annee_id' => 'required|exists:annees_academiques,id',
        ]);

        $etudiants = Etudiant::whereHas('inscriptions', function($q) use ($request) {
            $q->where('classe_id', $request->classe_id);
        })->get();

        foreach ($etudiants as $etudiant) {
            $this->calculerSemestre($etudiant->id, $request->semestre_id, $request->annee_id);
        }

        return back()->with('success', "Délibération terminée pour " . $etudiants->count() . " étudiants.");
    }
}