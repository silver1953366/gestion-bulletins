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

class BulletinController extends Controller
{
    /**
     * Afficher la liste des bulletins
     */
    public function index()
    {
        $etudiants = Etudiant::orderBy('nom')->orderBy('prenom')->get();
        $bulletins = Bulletin::with(['etudiant', 'anneeAcademique'])
            ->latest()
            ->paginate(15);
        
        return view('secretariat.bulletins.index', compact('etudiants', 'bulletins'));
    }

    /**
     * Générer un bulletin individuel
     */
    public function generate(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'type' => 'required|in:S5,S6,ANNUEL',
        ]);

        $etudiant = Etudiant::findOrFail($request->etudiant_id);
        $type = $request->type;
        
        // Récupérer l'année académique active
        $anneeAcademique = AnneeAcademique::where('active', true)->first();
        
        if (!$anneeAcademique) {
            return back()->with('error', 'Aucune année académique active trouvée');
        }

        // Vérifier si un bulletin existe déjà
        $existingBulletin = Bulletin::where('etudiant_id', $etudiant->id)
            ->where('type', $type)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->first();

        if ($existingBulletin) {
            return back()->with('warning', 'Un bulletin existe déjà pour cet étudiant');
        }

        DB::beginTransaction();
        
        try {
            // Générer les données du bulletin
            $bulletinData = $this->prepareBulletinData($etudiant, $type, $anneeAcademique);
            
            // Générer le PDF (à implémenter avec DomPDF ou autre)
            $pdfPath = $this->generatePDF($bulletinData);
            
            // Créer l'enregistrement du bulletin
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
     * Télécharger un bulletin
     */
    public function download($id)
    {
        $bulletin = Bulletin::findOrFail($id);
        
        $filePath = storage_path('app/public/' . $bulletin->fichier_pdf);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Fichier PDF introuvable');
        }
        
        return response()->download($filePath, "bulletin_{$bulletin->etudiant_id}_{$bulletin->type}.pdf");
    }

    /**
     * Export massif des bulletins pour une classe
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|in:S5,S6,ANNUEL',
        ]);

        $classeId = $request->classe_id;
        $type = $request->type;
        
        // Récupérer les étudiants de la classe
        $inscriptions = Inscription::where('classe_id', $classeId)
            ->with('etudiant')
            ->get();
        
        $anneeAcademique = AnneeAcademique::where('active', true)->first();
        
        $generated = 0;
        $errors = [];
        
        foreach ($inscriptions as $inscription) {
            $etudiant = $inscription->etudiant;
            
            // Vérifier si le bulletin existe déjà
            $existingBulletin = Bulletin::where('etudiant_id', $etudiant->id)
                ->where('type', $type)
                ->where('annee_academique_id', $anneeAcademique->id)
                ->first();
            
            if ($existingBulletin) {
                continue;
            }
            
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

    /**
     * Préparer les données pour le bulletin
     */
    private function prepareBulletinData($etudiant, $type, $anneeAcademique)
    {
        $data = [
            'etudiant' => $etudiant,
            'type' => $type,
            'annee_academique' => $anneeAcademique,
            'matieres' => [],
            'moyenne_generale' => 0,
            'credits_obtenus' => 0,
            'mention' => '',
            'decision' => '',
        ];
        
        // Récupérer les résultats selon le type
        if ($type == 'ANNUEL') {
            $resultatAnnuel = ResultatAnnuel::where('etudiant_id', $etudiant->id)
                ->where('annee_academique_id', $anneeAcademique->id)
                ->first();
            
            if ($resultatAnnuel) {
                $data['moyenne_generale'] = $resultatAnnuel->moyenne;
                $data['decision'] = $resultatAnnuel->decision;
                $data['mention'] = $resultatAnnuel->mention ?? $this->getMention($resultatAnnuel->moyenne);
            }
            
            // Récupérer tous les résultats par matière pour l'année
            $data['matieres'] = ResultatMatiere::where('etudiant_id', $etudiant->id)
                ->with('matiere')
                ->get();
        } else {
            // Pour S5 ou S6, récupérer les résultats du semestre
            $semestreLibelle = $type; // S5 ou S6
            $resultatSemestre = ResultatSemestre::where('etudiant_id', $etudiant->id)
                ->whereHas('semestre', function($q) use ($semestreLibelle) {
                    $q->where('libelle', $semestreLibelle);
                })
                ->where('annee_academique_id', $anneeAcademique->id)
                ->first();
            
            if ($resultatSemestre) {
                $data['moyenne_generale'] = $resultatSemestre->moyenne;
                $data['credits_obtenus'] = $resultatSemestre->credits_total;
                $data['decision'] = $resultatSemestre->valide ? 'VALIDÉ' : 'NON VALIDÉ';
                $data['mention'] = $this->getMention($resultatSemestre->moyenne);
            }
            
            // Récupérer les résultats par matière pour le semestre
            $data['matieres'] = ResultatMatiere::where('etudiant_id', $etudiant->id)
                ->whereHas('matiere.ue.semestre', function($q) use ($semestreLibelle) {
                    $q->where('libelle', $semestreLibelle);
                })
                ->with('matiere')
                ->get();
        }
        
        return $data;
    }

    /**
     * Obtenir la mention en fonction de la moyenne
     */
    private function getMention($moyenne)
    {
        if ($moyenne >= 16) return 'TRÈS BIEN';
        if ($moyenne >= 14) return 'BIEN';
        if ($moyenne >= 12) return 'ASSEZ BIEN';
        if ($moyenne >= 10) return 'PASSABLE';
        return 'INSUFFISANT';
    }

    /**
     * Générer le fichier PDF
     * Note: Vous devez installer barryvdh/laravel-dompdf pour cette fonction
     */
    private function generatePDF($data)
    {
        // Installer DomPDF: composer require barryvdh/laravel-dompdf
        // Puis décommentez le code ci-dessous
        
        /*
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('secretariat.bulletins.pdf', $data);
        $filename = 'bulletins/bulletin_' . $data['etudiant']->id . '_' . $data['type'] . '_' . time() . '.pdf';
        $path = storage_path('app/public/' . $filename);
        $pdf->save($path);
        return $filename;
        */
        
        // Version temporaire sans PDF réel
        $filename = 'bulletins/temp_bulletin_' . $data['etudiant']->id . '_' . time() . '.pdf';
        return $filename;
    }
}