<?php
// app/Http/Controllers/Secretariat/NoteController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Inscription;
use App\Models\ResultatMatiere;
use Illuminate\Support\Facades\DB;
use App\Models\AnneeAcademique;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $matieres = Matiere::with('ue.semestre.classe')->get();
        $selectedMatiereId = $request->get('matiere');
        $selectedMatiere = null;
        $etudiants = collect();
        
        if ($selectedMatiereId) {
            $selectedMatiere = Matiere::find($selectedMatiereId);
            $etudiants = $this->getEtudiantsByMatiere($selectedMatiereId);
        }
        
        return view('secretariat.notes.index', [
            'matieres' => $matieres,
            'selectedMatiereId' => $selectedMatiereId,
            'selectedMatiere' => $selectedMatiere,
            'etudiants' => $etudiants,
        ]);
    }
    
    public function saveNote(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type' => 'required|in:CC,EXAMEN,RATTRAPAGE',
            'note' => 'required|numeric|min:0|max:20',
        ]);
        
        DB::beginTransaction();
        try {
            // Sauvegarder la note
            $evaluation = Evaluation::updateOrCreate(
                [
                    'etudiant_id' => $request->etudiant_id,
                    'matiere_id' => $request->matiere_id,
                    'type' => $request->type,
                ],
                [
                    'note' => $request->note,
                    'created_by' => Auth::id(),
                ]
            );
            
            // Recalculer la moyenne
            $moyenne = $this->calculateMoyenneMatiere($request->etudiant_id, $request->matiere_id);
            
            // Mettre à jour le résultat
            if ($moyenne !== null) {
                ResultatMatiere::updateOrCreate(
                    [
                        'etudiant_id' => $request->etudiant_id,
                        'matiere_id' => $request->matiere_id,
                    ],
                    ['moyenne' => $moyenne]
                );
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Note sauvegardée',
                'nouvelle_moyenne' => $moyenne
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function batchSave(Request $request)
    {
        $request->validate([
            'notes' => 'required|array',
            'notes.*.etudiant_id' => 'required|exists:etudiants,id',
            'notes.*.matiere_id' => 'required|exists:matieres,id',
            'notes.*.type' => 'required|in:CC,EXAMEN,RATTRAPAGE',
            'notes.*.note' => 'required|numeric|min:0|max:20',
        ]);
        
        $saved = 0;
        foreach ($request->notes as $noteData) {
            try {
                Evaluation::updateOrCreate(
                    [
                        'etudiant_id' => $noteData['etudiant_id'],
                        'matiere_id' => $noteData['matiere_id'],
                        'type' => $noteData['type'],
                    ],
                    [
                        'note' => $noteData['note'],
                        'created_by' => Auth::id(),
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                continue;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$saved} note(s) sauvegardée(s)"
        ]);
    }
    
    public function export(Request $request)
    {
        $matiereId = $request->get('matiere');
        $matiere = Matiere::findOrFail($matiereId);
        $etudiants = $this->getEtudiantsByMatiere($matiereId);
        
        $filename = 'notes_' . $matiere->code . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($etudiants, $matiere) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['N°', 'Matricule', 'Nom', 'Prénom', 'CC /20', 'Examen /20', 'Moyenne /20']);
            
            foreach ($etudiants as $index => $etudiant) {
                fputcsv($file, [
                    $index + 1,
                    $etudiant['matricule'] ?? '',
                    $etudiant['nom'] ?? '',
                    $etudiant['prenom'] ?? '',
                    $etudiant['notes']['cc'] ?? '',
                    $etudiant['notes']['examen'] ?? '',
                    number_format($etudiant['moyenne'] ?? 0, 2),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function getEtudiantsByMatiere($matiereId)
    {
        $matiere = Matiere::with('ue.semestre.classe')->find($matiereId);
        
        if (!$matiere || !$matiere->ue || !$matiere->ue->semestre || !$matiere->ue->semestre->classe) {
            return collect();
        }
        
        $classeId = $matiere->ue->semestre->classe->id;
        $inscriptions = Inscription::where('classe_id', $classeId)->with('etudiant')->get();
        
        $etudiants = [];
        foreach ($inscriptions as $inscription) {
            $etudiant = $inscription->etudiant;
            if ($etudiant) {
                $cc = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->where('type', 'CC')
                    ->first();
                    
                $examen = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->where('type', 'EXAMEN')
                    ->first();
                
                $resultat = ResultatMatiere::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->first();
                
                $moyenne = $resultat ? $resultat->moyenne : null;
                
                $etudiants[] = [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->nom,
                    'prenom' => $etudiant->prenom,
                    'matricule' => $inscription->matricule ?? null,
                    'notes' => [
                        'cc' => $cc ? $cc->note : null,
                        'examen' => $examen ? $examen->note : null,
                    ],
                    'moyenne' => $moyenne,
                ];
            }
        }
        
        return collect($etudiants);
    }
    
    private function calculateMoyenneMatiere($etudiantId, $matiereId)
    {
        $cc = Evaluation::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->where('type', 'CC')
            ->value('note');
            
        $examen = Evaluation::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->where('type', 'EXAMEN')
            ->value('note');
        
        $rattrapage = Evaluation::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->where('type', 'RATTRAPAGE')
            ->value('note');
        
        if ($rattrapage !== null) {
            return $rattrapage;
        }
        
        $notes = 0;
        $ponderation = 0;
        
        if ($cc !== null) {
            $notes += $cc * 0.4;
            $ponderation += 0.4;
        }
        
        if ($examen !== null) {
            $notes += $examen * 0.6;
            $ponderation += 0.6;
        }
        
        if ($ponderation > 0) {
            return round($notes / $ponderation, 2);
        }
        
        return null;
    }
}