<?php
// app/Http/Controllers/Secretariat/BulletinController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\Bulletin;
use App\Models\AnneeAcademique;
use App\Models\Inscription;
use App\Models\Evaluation;
use App\Models\ResultatMatiere;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    public function index()
    {
        $etudiants = Etudiant::orderBy('nom')->orderBy('prenom')->get();
        $bulletins = Bulletin::with(['etudiant', 'anneeAcademique'])
            ->latest()
            ->paginate(15);
        
        return view('secretariat.bulletins.index', compact('etudiants', 'bulletins'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'type' => 'required|in:S5,S6,ANNUEL',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $type = $request->type;
        
        $anneeAcademique = AnneeAcademique::where('active', true)->first();
        
        if (!$anneeAcademique) {
            return back()->with('error', 'Aucune année académique active trouvée');
        }

        try {
            DB::beginTransaction();
            
            $bulletinData = $this->prepareBulletinData($etudiant, $type, $anneeAcademique);
            $pdfPath = $this->generatePDF($bulletinData);
            
            $bulletin = Bulletin::create([
                'etudiant_id' => $etudiant->id,
                'annee_academique_id' => $anneeAcademique->id,
                'type' => $type,
                'fichier_pdf' => $pdfPath,
                'generated_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('secretariat.bulletins.index')
                ->with('success', 'Bulletin généré avec succès');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la génération du bulletin: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger un bulletin - CORRIGÉ
     */
    public function download($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        
        $filePath = storage_path('app/public/' . $bulletin->fichier_pdf);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Fichier PDF introuvable');
        }
        
        // Forcer le téléchargement avec les bons headers
        return response()->download($filePath, "bulletin_{$bulletin->etudiant_id}_{$bulletin->type}.pdf", [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="bulletin_' . $bulletin->etudiant_id . '_' . $bulletin->type . '.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|in:S5,S6,ANNUEL',
        ]);

        $classeId = $request->classe_id;
        $type = $request->type;
        
        $inscriptions = Inscription::where('classe_id', $classeId)
            ->with('etudiant')
            ->get();
        
        $anneeAcademique = AnneeAcademique::where('active', true)->first();
        
        $generated = 0;
        $errors = [];
        
        foreach ($inscriptions as $inscription) {
            $etudiant = $inscription->etudiant;
            
            try {
                $bulletinData = $this->prepareBulletinData($etudiant, $type, $anneeAcademique);
                $pdfPath = $this->generatePDF($bulletinData);
                
                Bulletin::create([
                    'etudiant_id' => $etudiant->id,
                    'annee_academique_id' => $anneeAcademique->id,
                    'type' => $type,
                    'fichier_pdf' => $pdfPath,
                    'generated_at' => now(),
                ]);
                
                $generated++;
            } catch (\Exception $e) {
                $errors[] = $etudiant->nom . ' ' . $etudiant->prenom . ': ' . $e->getMessage();
            }
        }
        
        $message = "{$generated} bulletin(s) généré(s)";
        if (!empty($errors)) {
            $message .= ". Erreurs: " . implode(', ', $errors);
        }
        
        return redirect()->route('secretariat.bulletins.index')
            ->with('success', $message);
    }

    private function prepareBulletinData($etudiant, $type, $anneeAcademique)
    {
        $matieres = collect();
        $moyenneGenerale = 0;
        $creditsObtenus = 0;
        $decision = 'En attente';
        $mention = '';
        
        if ($type == 'ANNUEL') {
            $resultatAnnuel = ResultatAnnuel::where('etudiant_id', $etudiant->id)
                ->where('annee_academique_id', $anneeAcademique->id)
                ->first();
            
            if ($resultatAnnuel) {
                $moyenneGenerale = $resultatAnnuel->moyenne;
                $decision = $resultatAnnuel->decision;
                $mention = $resultatAnnuel->mention ?? $this->getMention($resultatAnnuel->moyenne);
            }
            
            $matieres = ResultatMatiere::where('etudiant_id', $etudiant->id)
                ->with('matiere.ue')
                ->get();
                
            $creditsObtenus = $matieres->sum(function($r) {
                return ($r->moyenne >= 10) ? ($r->matiere->credits ?? 0) : 0;
            });
        } else {
            $semestreLibelle = $type;
            $resultatSemestre = ResultatSemestre::where('etudiant_id', $etudiant->id)
                ->whereHas('semestre', function($q) use ($semestreLibelle) {
                    $q->where('libelle', $semestreLibelle);
                })
                ->where('annee_academique_id', $anneeAcademique->id)
                ->first();
            
            if ($resultatSemestre) {
                $moyenneGenerale = $resultatSemestre->moyenne;
                $creditsObtenus = $resultatSemestre->credits_total;
                $decision = $resultatSemestre->valide ? 'VALIDÉ' : 'NON VALIDÉ';
                $mention = $this->getMention($resultatSemestre->moyenne);
            }
            
            $matieres = ResultatMatiere::where('etudiant_id', $etudiant->id)
                ->whereHas('matiere.ue.semestre', function($q) use ($semestreLibelle) {
                    $q->where('libelle', $semestreLibelle);
                })
                ->with('matiere.ue')
                ->get();
        }
        
        return [
            'etudiant' => $etudiant,
            'type' => $type,
            'annee_academique' => $anneeAcademique,
            'matieres' => $matieres,
            'moyenne_generale' => $moyenneGenerale,
            'credits_obtenus' => $creditsObtenus,
            'mention' => $mention,
            'decision' => $decision,
        ];
    }

    private function getMention($moyenne)
    {
        if ($moyenne >= 16) return 'TRÈS BIEN';
        if ($moyenne >= 14) return 'BIEN';
        if ($moyenne >= 12) return 'ASSEZ BIEN';
        if ($moyenne >= 10) return 'PASSABLE';
        return 'INSUFFISANT';
    }

    private function generatePDF($data)
    {
        // Créer le dossier s'il n'existe pas
        $directory = storage_path('app/public/bulletins');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Générer le PDF
        $pdf = Pdf::loadView('secretariat.bulletins.pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        // Nom du fichier
        $filename = 'bulletins/bulletin_' . $data['etudiant']->id . '_' . $data['type'] . '_' . time() . '.pdf';
        $fullPath = storage_path('app/public/' . $filename);
        
        // Sauvegarder le PDF
        $pdf->save($fullPath);
        
        return $filename;
    }
}