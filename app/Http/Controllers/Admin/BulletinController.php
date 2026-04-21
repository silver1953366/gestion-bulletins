<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use App\Models\UniteEnseignement;
use App\Models\Note;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BulletinController extends Controller
{
    public function index()
    {
        $bulletins = Bulletin::with(['etudiant', 'anneeAcademique'])->latest()->paginate(15);
        return view('admin.bulletins.index', compact('bulletins'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'type' => 'required|in:S5,S6,ANNUEL',
            'annee_academique_id' => 'required|exists:annees_academiques,id'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $etudiant = Etudiant::findOrFail($request->etudiant_id);
                $type = $request->type;
                $anneeId = $request->annee_academique_id;

                // 1. Préparation des données avec statistiques
                $data = $this->prepareBulletinData($etudiant, $type, $anneeId);

                // 2. Sélection de la vue
                $view = match($type) {
                    'S5' => 'admin.bulletins.pdf.s5',
                    'S6' => 'admin.bulletins.pdf.s6',
                    'ANNUEL' => 'admin.bulletins.pdf.annuel',
                };

                // 3. Génération PDF
                $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'portrait');

                // 4. Stockage
                $fileName = "bulletins/{$type}_" . str_replace(' ', '_', $etudiant->last_name) . "_" . now()->format('YmdHis') . ".pdf";
                Storage::disk('public')->put($fileName, $pdf->output());

                // 5. Enregistrement en BDD
                $bulletin = Bulletin::create([
                    'etudiant_id' => $etudiant->id,
                    'annee_academique_id' => $anneeId,
                    'type' => $type,
                    'fichier_pdf' => $fileName,
                    'generated_at' => now(),
                ]);

                // 6. Journal d'audit
                AuditLog::log('CREATE', 'Bulletin', $bulletin->id, null, [
                    'type' => $type, 
                    'etudiant' => $etudiant->full_name
                ]);

                return back()->with('success', "Bulletin {$type} généré pour {$etudiant->full_name}.");
            });
        } catch (\Exception $e) {
            return back()->with('error', "Erreur : " . $e->getMessage());
        }
    }

    private function prepareBulletinData($etudiant, $type, $anneeId)
    {
        $annee = AnneeAcademique::findOrFail($anneeId);
        $semestres = ($type === 'ANNUEL') ? [5, 6] : [($type === 'S5' ? 5 : 6)];

        $ues = UniteEnseignement::whereIn('semestre', $semestres)
            ->with(['matieres.notes' => function($q) use ($etudiant, $anneeId) {
                $q->where('etudiant_id', $etudiant->id)->where('annee_academique_id', $anneeId);
            }])->get();

        $totalPointsGlobal = 0;
        $totalCoeffsGlobal = 0;
        $totalCreditsAcquis = 0;
        $resultats_annuels = [];

        foreach ($ues as $ue) {
            $uePoints = 0;
            $ueCoeffs = 0;
            $ueCreditsPossible = 0;

            foreach ($ue->matieres as $matiere) {
                $notes = $matiere->notes;
                
                // Calcul selon règle 40/60 
                $cc = $notes->where('type', 'CC')->first()?->valeur ?? 0;
                $exam = $notes->where('type', 'EXAMEN')->first()?->valeur ?? 0;
                $rattrapage = $notes->where('type', 'RATTRAPAGE')->first()?->valeur;

                $moyenneMatiere = ($cc * 0.4) + ($exam * 0.6);
                if ($rattrapage !== null && $rattrapage > $moyenneMatiere) {
                    $moyenneMatiere = $rattrapage;
                }

                $matiere->note_finale = $moyenneMatiere;

                // Statistiques de promotion (Exigence 5.5) 
                $allNotesMatiere = Note::where('matiere_id', $matiere->id)
                    ->where('annee_academique_id', $anneeId)
                    ->pluck('valeur');

                $matiere->moyenne_classe = $allNotesMatiere->avg() ?? 0;
                $matiere->min_classe = $allNotesMatiere->min() ?? 0;
                $matiere->max_classe = $allNotesMatiere->max() ?? 0;
                $matiere->ecart_type = $this->calculateStandardDeviation($allNotesMatiere->toArray());

                $uePoints += ($moyenneMatiere * $matiere->coefficient);
                $ueCoeffs += $matiere->coefficient;
                $ueCreditsPossible += $matiere->credits;
            }

            $ue->moyenne = $ueCoeffs > 0 ? $uePoints / $ueCoeffs : 0;
            $ue->total_credits = $ueCreditsPossible;
            $ue->total_coeffs = $ueCoeffs;

            if ($ue->moyenne >= 10) {
                $totalCreditsAcquis += $ueCreditsPossible;
            }

            $totalPointsGlobal += $uePoints;
            $totalCoeffsGlobal += $ueCoeffs;

            if ($type === 'ANNUEL') {
                $resultats_annuels[] = [
                    'ue_libelle' => $ue->libelle,
                    'coeff' => $ueCoeffs,
                    'moy_s5' => ($ue->semestre == 5) ? $ue->moyenne : null,
                    'moy_s6' => ($ue->semestre == 6) ? $ue->moyenne : null,
                    'moy_annuelle' => $ue->moyenne,
                    'statut' => ($ue->moyenne >= 10) ? 'VALIDÉ' : 'AJOURNÉ'
                ];
            }
        }

        $moyenneGale = $totalCoeffsGlobal > 0 ? $totalPointsGlobal / $totalCoeffsGlobal : 0;

        return [
            'etudiant' => $etudiant,
            'annee' => $annee,
            'unites_enseignement' => $ues,
            'moyenne_semestre' => $moyenneGale,
            'total_annee' => $moyenneGale,
            'total_credits' => $totalCreditsAcquis,
            'decision' => $moyenneGale >= 10 ? 'ADMIS' : 'AJOURNÉ',
            'mention' => $this->getMention($moyenneGale),
            'resultats_annuels' => $resultats_annuels,
            'date' => now()->format('d/m/Y'),
            'type' => $type
        ];
    }

    private function calculateStandardDeviation($array) {
        $fCount = count($array);
        if ($fCount === 0) return 0;
        $fMean = array_sum($array) / $fCount;
        $fVariance = 0;
        foreach ($array as $i) { $fVariance += pow($i - $fMean, 2); }
        return sqrt($fVariance / $fCount);
    }

    private function getMention($moyenne) {
        if ($moyenne >= 16) return 'Très Bien';
        if ($moyenne >= 14) return 'Bien';
        if ($moyenne >= 12) return 'Assez Bien';
        if ($moyenne >= 10) return 'Passable';
        return 'Insuffisant';
    }

    public function download(Bulletin $bulletin)
    {
        if (!Storage::disk('public')->exists($bulletin->fichier_pdf)) {
            return back()->with('error', "Fichier introuvable.");
        }
        return Storage::disk('public')->download($bulletin->fichier_pdf);
    }

    public function destroy(Bulletin $bulletin)
    {
        Storage::disk('public')->delete($bulletin->fichier_pdf);
        $bulletin->delete();
        return back()->with('success', "Bulletin supprimé.");
    }
}