<?php
// app/Http/Controllers/Enseignant/DashboardController.php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\TeacherProfile;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Absence;
use App\Models\Inscription;
use App\Models\ResultatMatiere;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'enseignant connecté
        $user = Auth::user();
        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        
        if (!$teacherProfile) {
            abort(403, 'Profil enseignant non trouvé');
        }

        // Récupérer les matières de l'enseignant
        $matieresEnseignant = DB::table('enseignant_matiere')
            ->where('teacher_profile_id', $teacherProfile->id)
            ->pluck('matiere_id');
        
        $matieres = Matiere::whereIn('id', $matieresEnseignant)
            ->with('ue.semestre.classe')
            ->get();
        
        // Statistiques globales
        $stats = $this->getTeacherStats($teacherProfile->id, $matieresEnseignant);
        
        // Matière sélectionnée
        $selectedMatiereId = $request->get('matiere');
        $selectedMatiere = null;
        $etudiants = collect();
        $statsMatiere = [];
        
        if ($selectedMatiereId && $matieresEnseignant->contains($selectedMatiereId)) {
            $selectedMatiere = Matiere::find($selectedMatiereId);
            $etudiants = $this->getStudentsForMatiere($selectedMatiereId);
            $statsMatiere = $this->getMatiereStats($selectedMatiereId);
        }
        
        // Préparer les données pour les matières
        $matieresData = [];
        foreach ($matieres as $matiere) {
            $matieresData[] = $this->getMatiereSummary($matiere);
        }
        
        return view('enseignant.dashboard', [
            'stats' => $stats,
            'matieres' => $matieresData,
            'selectedMatiereId' => $selectedMatiereId,
            'selectedMatiere' => $selectedMatiere,
            'etudiants' => $etudiants,
            'statsMatiere' => $statsMatiere,
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
        
        // Vérifier que l'enseignant a le droit sur cette matière
        $user = Auth::user();
        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        
        $hasMatiere = DB::table('enseignant_matiere')
            ->where('teacher_profile_id', $teacherProfile->id)
            ->where('matiere_id', $request->matiere_id)
            ->exists();
            
        if (!$hasMatiere) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé'], 403);
        }
        
        // Sauvegarder ou mettre à jour la note
        $evaluation = Evaluation::updateOrCreate(
            [
                'etudiant_id' => $request->etudiant_id,
                'matiere_id' => $request->matiere_id,
                'type' => $request->type,
            ],
            [
                'note' => $request->note,
                'created_by' => $user->id,
            ]
        );
        
        // Recalculer la moyenne de la matière pour l'étudiant
        $moyenne = $this->calculateMoyenneMatiere($request->etudiant_id, $request->matiere_id);
        
        // Mettre à jour ou créer le résultat matière
        ResultatMatiere::updateOrCreate(
            [
                'etudiant_id' => $request->etudiant_id,
                'matiere_id' => $request->matiere_id,
            ],
            [
                'moyenne' => $moyenne,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Note sauvegardée',
            'nouvelle_moyenne' => $moyenne,
        ]);
    }
    
    public function exportNotes(Request $request)
    {
        $matiereId = $request->get('matiere');
        
        // Vérification des droits
        $user = Auth::user();
        $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
        
        $hasMatiere = DB::table('enseignant_matiere')
            ->where('teacher_profile_id', $teacherProfile->id)
            ->where('matiere_id', $matiereId)
            ->exists();
            
        if (!$hasMatiere) {
            abort(403);
        }
        
        $etudiants = $this->getStudentsForMatiere($matiereId);
        $matiere = Matiere::find($matiereId);
        
        $filename = 'notes_' . $matiere->code . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($etudiants, $matiere) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['N°', 'Matricule', 'Nom', 'Prénom', 'CC /20', 'Examen /20', 'Moyenne /20', 'Appréciation']);
            
            foreach ($etudiants as $index => $etudiant) {
                $appreciation = ($etudiant['moyenne'] ?? 0) >= 10 ? 'Admis' : 'Non admis';
                fputcsv($file, [
                    $index + 1,
                    $etudiant['matricule'] ?? '',
                    $etudiant['nom'] ?? '',
                    $etudiant['prenom'] ?? '',
                    $etudiant['notes']['cc'] ?? '',
                    $etudiant['notes']['examen'] ?? '',
                    number_format($etudiant['moyenne'] ?? 0, 2),
                    $appreciation,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function getTeacherStats($teacherProfileId, $matieresIds)
    {
        $totalMatieres = $matieresIds->count();
        
        // Récupérer tous les étudiants inscrits dans les classes concernées
        $etudiantsIds = DB::table('enseignant_matiere')
            ->join('matieres', 'enseignant_matiere.matiere_id', '=', 'matieres.id')
            ->join('ues', 'matieres.ue_id', '=', 'ues.id')
            ->join('semestres', 'ues.semestre_id', '=', 'semestres.id')
            ->join('classes', 'semestres.classe_id', '=', 'classes.id')
            ->join('inscriptions', 'inscriptions.classe_id', '=', 'classes.id')
            ->where('enseignant_matiere.teacher_profile_id', $teacherProfileId)
            ->pluck('inscriptions.etudiant_id')
            ->unique();
        
        $totalEtudiants = $etudiantsIds->count();
        
        // Nombre de notes saisies
        $notesSaisies = Evaluation::whereIn('matiere_id', $matieresIds)
            ->count();
        
        $notesAttendues = $totalMatieres * $totalEtudiants * 2; // CC + Examen
        
        // Total des absences
        $totalAbsences = Absence::whereIn('matiere_id', $matieresIds)
            ->sum('heures');
        
        return [
            'total_matieres' => $totalMatieres,
            'total_etudiants' => $totalEtudiants,
            'notes_saisies' => $notesSaisies,
            'notes_attendues' => $notesAttendues,
            'taux_remplissage' => $notesAttendues > 0 ? ($notesSaisies / $notesAttendues) * 100 : 0,
            'total_absences' => $totalAbsences,
        ];
    }
    
    private function getStudentsForMatiere($matiereId)
    {
        // Récupérer la classe associée à la matière via UE -> Semestre -> Classe
        $matiere = Matiere::with('ue.semestre.classe')->find($matiereId);
        
        if (!$matiere || !$matiere->ue || !$matiere->ue->semestre || !$matiere->ue->semestre->classe) {
            return collect();
        }
        
        $classeId = $matiere->ue->semestre->classe->id;
        
        // Récupérer les inscriptions actives
        $inscriptions = Inscription::where('classe_id', $classeId)
            ->with('etudiant')
            ->get();
        
        $etudiants = [];
        foreach ($inscriptions as $inscription) {
            $etudiant = $inscription->etudiant;
            if ($etudiant) {
                // Récupérer les notes
                $cc = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->where('type', 'CC')
                    ->first();
                    
                $examen = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->where('type', 'EXAMEN')
                    ->first();
                
                // Récupérer le résultat matière existant
                $resultat = ResultatMatiere::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiereId)
                    ->first();
                
                $moyenne = $resultat ? $resultat->moyenne : $this->calculateMoyenneMatiere($etudiant->id, $matiereId);
                
                $etudiants[] = [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->nom,
                    'prenom' => $etudiant->prenom,
                    'matricule' => $inscription->matricule ?? null,
                    'classe' => $matiere->ue->semestre->classe->nom ?? 'N/A',
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
        
        // Si rattrapage existe, l'utiliser comme note finale
        if ($rattrapage !== null) {
            return $rattrapage;
        }
        
        // Sinon moyenne CC (40%) + Examen (60%)
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
    
    private function getMatiereStats($matiereId)
    {
        $etudiants = $this->getStudentsForMatiere($matiereId);
        
        if ($etudiants->isEmpty()) {
            return [
                'moyenne_classe' => 0,
                'taux_reussite' => 0,
                'meilleure_note' => 0,
            ];
        }
        
        $moyennes = $etudiants->pluck('moyenne')->filter();
        $reussis = $moyennes->filter(function($m) { return $m >= 10; })->count();
        
        return [
            'moyenne_classe' => $moyennes->avg() ?? 0,
            'taux_reussite' => $etudiants->count() > 0 ? ($reussis / $etudiants->count()) * 100 : 0,
            'meilleure_note' => $moyennes->max() ?? 0,
        ];
    }
    
    private function getMatiereSummary($matiere)
    {
        $etudiants = $this->getStudentsForMatiere($matiere->id);
        $notesSaisies = Evaluation::where('matiere_id', $matiere->id)->count();
        $notesAttendues = $etudiants->count() * 2;
        
        $moyennes = $etudiants->pluck('moyenne')->filter();
        
        return [
            'id' => $matiere->id,
            'libelle' => $matiere->libelle,
            'coefficient' => $matiere->coefficient,
            'credits' => $matiere->credits,
            'total_etudiants' => $etudiants->count(),
            'notes_saisies' => $notesSaisies,
            'taux_remplissage' => $notesAttendues > 0 ? ($notesSaisies / $notesAttendues) * 100 : 0,
            'moyenne_classe' => $moyennes->avg() ?? 0,
        ];
    }

  // app/Http/Controllers/Enseignant/DashboardController.php

// Ajoutez cette méthode
public function statistiques(Request $request)
{
    $user = Auth::user();
    $teacherProfile = TeacherProfile::where('user_id', $user->id)->first();
    
    if (!$teacherProfile) {
        abort(403, 'Profil enseignant non trouvé');
    }

    // Récupérer les matières de l'enseignant
    $matieresEnseignant = DB::table('enseignant_matiere')
        ->where('teacher_profile_id', $teacherProfile->id)
        ->pluck('matiere_id');
    
    $matieres = Matiere::whereIn('id', $matieresEnseignant)->get();
    
    $selectedMatiereId = $request->get('matiere');
    $selectedMatiere = null;
    $statistiques = null;
    
    if ($selectedMatiereId && $matieresEnseignant->contains($selectedMatiereId)) {
        $selectedMatiere = Matiere::find($selectedMatiereId);
        $statistiques = $this->getDetailedStats($selectedMatiereId);
    }
    
    // Préparer les données pour le sélecteur
    $matieresData = [];
    foreach ($matieres as $matiere) {
        $matieresData[] = [
            'id' => $matiere->id,
            'libelle' => $matiere->libelle,
            'coefficient' => $matiere->coefficient,
        ];
    }
    
    return view('enseignant.statistiques', [
        'matieres' => $matieresData,
        'selectedMatiereId' => $selectedMatiereId,
        'selectedMatiere' => $selectedMatiere,
        'statistiques' => $statistiques,
    ]);
}

// Ajoutez cette méthode helper
private function getDetailedStats($matiereId)
{
    $etudiants = $this->getStudentsForMatiere($matiereId);
    
    if ($etudiants->isEmpty()) {
        return [
            'moyenne_classe' => 0,
            'meilleure_note' => 0,
            'moins_bonne_note' => 0,
            'taux_reussite' => 0,
            'distribution' => [],
            'stats_cc' => ['moyenne' => 0, 'ecart_type' => 0, 'taux_saisie' => 0],
            'stats_examen' => ['moyenne' => 0, 'ecart_type' => 0, 'taux_saisie' => 0],
            'details_etudiants' => [],
        ];
    }
    
    $moyennes = $etudiants->pluck('moyenne')->filter();
    $notesCC = $etudiants->pluck('notes.cc')->filter();
    $notesExamen = $etudiants->pluck('notes.examen')->filter();
    
    // Distribution des notes
    $distribution = [
        '18-20' => 0,
        '16-17.99' => 0,
        '14-15.99' => 0,
        '12-13.99' => 0,
        '10-11.99' => 0,
        '0-9.99' => 0,
    ];
    
    foreach ($moyennes as $moyenne) {
        if ($moyenne >= 18) $distribution['18-20']++;
        elseif ($moyenne >= 16) $distribution['16-17.99']++;
        elseif ($moyenne >= 14) $distribution['14-15.99']++;
        elseif ($moyenne >= 12) $distribution['12-13.99']++;
        elseif ($moyenne >= 10) $distribution['10-11.99']++;
        else $distribution['0-9.99']++;
    }
    
    // Calcul écart-type pour CC
    $moyenneCC = $notesCC->avg() ?? 0;
    $ecartTypeCC = 0;
    if ($notesCC->count() > 0) {
        $variance = $notesCC->map(function($note) use ($moyenneCC) {
            return pow($note - $moyenneCC, 2);
        })->avg();
        $ecartTypeCC = sqrt($variance);
    }
    
    // Calcul écart-type pour Examen
    $moyenneExamen = $notesExamen->avg() ?? 0;
    $ecartTypeExamen = 0;
    if ($notesExamen->count() > 0) {
        $variance = $notesExamen->map(function($note) use ($moyenneExamen) {
            return pow($note - $moyenneExamen, 2);
        })->avg();
        $ecartTypeExamen = sqrt($variance);
    }
    
    $totalEtudiants = $etudiants->count();
    $reussis = $moyennes->filter(function($m) { return $m >= 10; })->count();
    
    // Détails des étudiants pour le tableau
    $detailsEtudiants = [];
    foreach ($etudiants as $etudiant) {
        $detailsEtudiants[] = [
            'nom' => $etudiant['nom'],
            'prenom' => $etudiant['prenom'],
            'matricule' => $etudiant['matricule'],
            'cc' => $etudiant['notes']['cc'] ?? 0,
            'examen' => $etudiant['notes']['examen'] ?? 0,
            'moyenne' => $etudiant['moyenne'] ?? 0,
        ];
    }
    
    return [
        'moyenne_classe' => $moyennes->avg() ?? 0,
        'meilleure_note' => $moyennes->max() ?? 0,
        'moins_bonne_note' => $moyennes->min() ?? 0,
        'taux_reussite' => $totalEtudiants > 0 ? ($reussis / $totalEtudiants) * 100 : 0,
        'distribution' => $distribution,
        'stats_cc' => [
            'moyenne' => $moyenneCC,
            'ecart_type' => $ecartTypeCC,
            'taux_saisie' => $totalEtudiants > 0 ? ($notesCC->count() / $totalEtudiants) * 100 : 0,
        ],
        'stats_examen' => [
            'moyenne' => $moyenneExamen,
            'ecart_type' => $ecartTypeExamen,
            'taux_saisie' => $totalEtudiants > 0 ? ($notesExamen->count() / $totalEtudiants) * 100 : 0,
        ],
        'details_etudiants' => $detailsEtudiants,
    ];
}
}