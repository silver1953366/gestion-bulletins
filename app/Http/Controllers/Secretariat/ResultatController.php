<?php
// app/Http/Controllers/Secretariat/ResultatController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\ResultatAnnuel;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\ResultatSemestre;
use Illuminate\Support\Facades\DB;

class ResultatController extends Controller
{
    public function index()
    {
        $resultats = ResultatAnnuel::with(['etudiant', 'anneeAcademique'])->paginate(20);
        return view('secretariat.resultats.index', compact('resultats'));
    }

    public function moyennes(Request $request)
    {
        // Calculer les statistiques par matière
        $matieres = Matiere::with(['resultatsMatieres', 'ue.semestre.classe'])->get();
        $statistiques = [];
        
        foreach ($matieres as $matiere) {
            $moyennes = $matiere->resultatsMatieres->pluck('moyenne')->filter();
            
            if ($moyennes->count() > 0) {
                $reussis = $moyennes->filter(function($m) { return $m >= 10; })->count();
                $statistiques[] = [
                    'matiere' => $matiere->libelle,
                    'classe' => $matiere->ue && $matiere->ue->semestre && $matiere->ue->semestre->classe ? $matiere->ue->semestre->classe->nom : 'N/A',
                    'nb_etudiants' => $moyennes->count(),
                    'min' => round($moyennes->min(), 2),
                    'max' => round($moyennes->max(), 2),
                    'moyenne' => round($moyennes->avg(), 2),
                    'taux_reussite' => round(($reussis / $moyennes->count()) * 100, 1),
                ];
            }
        }
        
        // Appliquer les filtres
        if ($request->classe_id) {
            $classe = Classe::find($request->classe_id);
            if ($classe) {
                $statistiques = array_filter($statistiques, function($stat) use ($classe) {
                    return $stat['classe'] == $classe->nom;
                });
            }
        }
        
        if ($request->matiere_id) {
            $matiere = Matiere::find($request->matiere_id);
            if ($matiere) {
                $statistiques = array_filter($statistiques, function($stat) use ($matiere) {
                    return $stat['matiere'] == $matiere->libelle;
                });
            }
        }
        
        // Réindexer le tableau après filtrage
        $statistiques = array_values($statistiques);
        
        return view('secretariat.resultats.moyennes', compact('statistiques'));
    }

    public function credits()
    {
        $etudiants = Etudiant::with(['inscriptions.classe', 'resultatsSemestres.semestre'])->get();
        $creditsStats = [];
        
        foreach ($etudiants as $etudiant) {
            $creditsS5 = 0;
            $creditsS6 = 0;
            
            foreach ($etudiant->resultatsSemestres as $resultat) {
                if ($resultat->semestre && $resultat->semestre->libelle == 'S5') {
                    $creditsS5 = $resultat->credits_total ?? 0;
                }
                if ($resultat->semestre && $resultat->semestre->libelle == 'S6') {
                    $creditsS6 = $resultat->credits_total ?? 0;
                }
            }
            
            $totalCredits = $creditsS5 + $creditsS6;
            $pourcentage = $totalCredits > 0 ? round(($totalCredits / 60) * 100, 1) : 0;
            
            $creditsStats[] = [
                'id' => $etudiant->id,
                'nom' => $etudiant->nom,
                'prenom' => $etudiant->prenom,
                'classe' => $etudiant->inscriptions->first() && $etudiant->inscriptions->first()->classe ? $etudiant->inscriptions->first()->classe->nom : 'Non inscrit',
                'credits_s5' => $creditsS5,
                'credits_s6' => $creditsS6,
                'total_credits' => $totalCredits,
                'pourcentage' => $pourcentage,
            ];
        }
        
        // Trier par total de crédits décroissant
        usort($creditsStats, function($a, $b) {
            return $b['total_credits'] <=> $a['total_credits'];
        });
        
        return view('secretariat.resultats.credits', compact('creditsStats'));
    }

    public function jury()
    {
        $resultats = ResultatAnnuel::with(['etudiant', 'anneeAcademique'])->get();
        
        // Calculer les moyennes si elles n'existent pas
        foreach ($resultats as $resultat) {
            if (!$resultat->moyenne) {
                // Calculer la moyenne à partir des semestres si disponible
                $moyenneS5 = ResultatSemestre::where('etudiant_id', $resultat->etudiant_id)
                    ->whereHas('semestre', function($q) {
                        $q->where('libelle', 'S5');
                    })
                    ->value('moyenne');
                    
                $moyenneS6 = ResultatSemestre::where('etudiant_id', $resultat->etudiant_id)
                    ->whereHas('semestre', function($q) {
                        $q->where('libelle', 'S6');
                    })
                    ->value('moyenne');
                
                $resultat->moyenne_s5 = $moyenneS5 ?? 0;
                $resultat->moyenne_s6 = $moyenneS6 ?? 0;
                $resultat->moyenne = ($moyenneS5 + $moyenneS6) / 2;
            } else {
                $resultat->moyenne_s5 = $resultat->moyenne ?? 0;
                $resultat->moyenne_s6 = $resultat->moyenne ?? 0;
            }
        }
        
        $stats = [
            'admis' => $resultats->where('decision', 'ADMIS')->count(),
            'redoublants' => $resultats->where('decision', 'REDOUBLEMENT')->count(),
            'exclus' => $resultats->where('decision', 'EXCLU')->count(),
            'total' => $resultats->count(),
        ];
        
        return view('secretariat.resultats.jury', compact('resultats', 'stats'));
    }

    public function validateJury(Request $request)
    {
        $request->validate([
            'resultats' => 'required|array',
            'resultats.*.id' => 'required|exists:resultats_annuels,id',
            'resultats.*.decision' => 'required|in:ADMIS,REDOUBLEMENT,EXCLU',
        ]);
        
        $updated = 0;
        foreach ($request->resultats as $data) {
            $resultat = ResultatAnnuel::find($data['id']);
            if ($resultat && $resultat->decision != $data['decision']) {
                $resultat->update(['decision' => $data['decision']]);
                $updated++;
            }
        }
        
        $message = $updated > 0 
            ? "{$updated} décision(s) du jury enregistrée(s) avec succès" 
            : "Aucune modification détectée";
        
        return redirect()->route('secretariat.resultats.jury')
            ->with('success', $message);
    }
}