<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatAnnuel;
use App\Models\ResultatSemestre;
use App\Models\ResultatUe;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ResultatAnnuelController extends Controller
{
    /**
     * Affiche la liste des résultats annuels
     */
    public function index(Request $request)
    {
        $classes = Classe::all();
        $annees = AnneeAcademique::all();

        $query = ResultatAnnuel::with(['etudiant.user', 'anneeAcademique']);

        // Filtrage par classe si demandé
        if ($request->filled('classe_id')) {
            $query->whereHas('etudiant.inscriptions', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }

        $resultats = $query->latest()->get();

        // Correction du chemin de la vue selon ta structure
        return view('admin.resultats.annuels', compact('resultats', 'classes', 'annees'));
    }

    /**
     * Calcule la décision annuelle finale (Diplômé, Redouble, etc.)
     */
    public function calculerDecisionAnnuelle($etudiantId, $anneeId)
    {
        $resultatsSemestres = ResultatSemestre::where('etudiant_id', $etudiantId)
            ->where('annee_academique_id', $anneeId)
            ->get();

        if ($resultatsSemestres->count() < 2) {
            return null; 
        }

        $moyenneAnnuelle = $resultatsSemestres->avg('moyenne');
        $totalCreditsAnnuels = $resultatsSemestres->sum('credits_total');

        $decision = "Redouble la Licence 3";
        
        if ($totalCreditsAnnuels >= 60) {
            $decision = "Diplômé(e)";
        } 
        else {
            $ueSoutenance = ResultatUe::where('etudiant_id', $etudiantId)
                ->whereHas('ue', function($q) {
                    $q->where('code', 'LIKE', '%UE6-2%')
                      ->orWhere('libelle', 'LIKE', '%Soutenance%');
                })->first();

            if ($ueSoutenance && !$ueSoutenance->isValide() && ($totalCreditsAnnuels >= (60 - $ueSoutenance->ue->matieres->sum('credit')))) {
                $decision = "Reprise de soutenance";
            }
        }

        $mention = "NÉANT";
        if ($moyenneAnnuelle >= 16) $mention = "TRES BIEN";
        elseif ($moyenneAnnuelle >= 14) $mention = "BIEN";
        elseif ($moyenneAnnuelle >= 12) $mention = "ASSEZ BIEN";
        elseif ($moyenneAnnuelle >= 10) $mention = "PASSABLE";

        return ResultatAnnuel::updateOrCreate(
            ['etudiant_id' => $etudiantId, 'annee_academique_id' => $anneeId],
            [
                'moyenne' => $moyenneAnnuelle,
                'decision' => $decision,
                'mention' => $mention
            ]
        );
    }

    /**
     * Traitement par lot pour une promotion entière
     */
    public function calculerPromo(Request $request)
    {
        $request->validate([
            'annee_id' => 'required|exists:annee_academiques,id', // Note: vérifie le nom de ta table (singulier/pluriel)
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