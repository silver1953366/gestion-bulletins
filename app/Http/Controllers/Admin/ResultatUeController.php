<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultatUe;
use App\Models\Ue;
use App\Models\ResultatMatiere;
use App\Models\Classe;
use Illuminate\Http\Request;

class ResultatUeController extends Controller
{
    /**
     * Liste des résultats par UE.
     */
    public function index(Request $request)
    {
        $ues = Ue::all();
        $resultats = ResultatUe::with(['etudiant', 'ue'])
            ->when($request->ue_id, fn($q) => $q->where('ue_id', $request->ue_id))
            ->latest()
            ->paginate(15);

        return view('admin.resultats.ues', compact('resultats', 'ues'));
    }

    /**
     * Calcule la moyenne d'une UE pour un étudiant.
     */
    public function calculerUe($etudiantId, $ueId)
    {
        $ue = Ue::with('matieres')->find($ueId);
        $totalPoints = 0;
        $totalCoeff = 0;

        foreach ($ue->matieres as $matiere) {
            $resMatiere = ResultatMatiere::where('etudiant_id', $etudiantId)
                                        ->where('matiere_id', $matiere->id)
                                        ->first();
            
            $moy = $resMatiere ? (float)$resMatiere->moyenne : 0;
            // On utilise les crédits de la matière comme coefficient
            $totalPoints += ($moy * $matiere->credit); 
            $totalCoeff += $matiere->credit;
        }

        $moyenneUe = $totalCoeff > 0 ? $totalPoints / $totalCoeff : 0;

        return ResultatUe::updateOrCreate(
            ['etudiant_id' => $etudiantId, 'ue_id' => $ueId],
            [
                'moyenne' => $moyenneUe,
                'credits_acquis' => ($moyenneUe >= 10) ? $totalCoeff : 0,
                'compense' => ($moyenneUe < 10 && $moyenneUe >= 8) // Seuil de compensation
            ]
        );
    }

    /**
     * Calcule toutes les UEs d'une classe pour les délibérations.
     */
    public function calculerClasse(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
        ]);

        $classe = Classe::with(['inscriptions.etudiant', 'niveau.ues'])->find($request->classe_id);
        
        foreach ($classe->inscriptions as $inscription) {
            foreach ($classe->niveau->ues as $ue) {
                $this->calculerUe($inscription->etudiant_id, $ue->id);
            }
        }

        return back()->with('success', 'Calcul des UEs terminé pour la classe.');
    }
}