<?php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Evaluation;
use App\Models\Absence;
use App\Models\ResultatAnnuel;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use App\Models\TeacherProfile;
use App\Models\StudentProfile;
use App\Models\Classe;
use App\Models\Semestre;
use App\Models\Ue;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $stats = $this->getGlobalStats();
        
        // Matières pour sélection rapide
        $matieres = Matiere::select('id', 'libelle', 'code')->take(10)->get();
        
        // Alertes système
        $alerts = $this->getAlerts();
        
        // Dernières inscriptions
        $recentInscriptions = $this->getRecentInscriptions();
        
        // Statistiques jury
        $juryStats = $this->getJuryStats();
        
        return view('secretariat.dashboard', [
            'stats' => $stats,
            'matieres' => $matieres,
            'alerts' => $alerts,
            'recentInscriptions' => $recentInscriptions,
            'juryStats' => $juryStats,
        ]);
    }
    
    private function getGlobalStats()
    {
        $totalEtudiants = Etudiant::count();
        
        // Taux de réussite - Attention: ResultatAnnuel peut être vide
        $reussis = 0;
        if (class_exists('App\Models\ResultatAnnuel')) {
            $resultats = ResultatAnnuel::whereNotNull('decision')->get();
            $reussis = $resultats->where('decision', 'ADMIS')->count();
        }
        $tauxReussite = $totalEtudiants > 0 ? ($reussis / $totalEtudiants) * 100 : 0;
        
        // Notes saisies vs attendues
        $notesSaisies = Evaluation::count();
        $totalMatieres = Matiere::count();
        $notesAttendues = $totalEtudiants * $totalMatieres * 2;
        $tauxSaisie = $notesAttendues > 0 ? ($notesSaisies / $notesAttendues) * 100 : 0;
        
        // Moyenne générale
        $moyenneGenerale = Evaluation::avg('note') ?? 0;
        
        return [
            'total_etudiants' => $totalEtudiants,
            'taux_reussite' => round($tauxReussite, 1),
            'notes_saisies' => $notesSaisies,
            'notes_attendues' => $notesAttendues,
            'taux_saisie_notes' => round($tauxSaisie, 1),
            'moyenne_generale' => round($moyenneGenerale, 2),
        ];
    }
    
    private function getAlerts()
    {
        $alerts = [];
        
        // Étudiants avec trop d'absences
        $absencesElevees = Absence::select('etudiant_id', DB::raw('SUM(heures) as total_heures'))
            ->groupBy('etudiant_id')
            ->having('total_heures', '>', 30)
            ->take(5)
            ->get();
            
        foreach ($absencesElevees as $absence) {
            $etudiant = Etudiant::find($absence->etudiant_id);
            if ($etudiant) {
                $alerts[] = [
                    'message' => "L'étudiant {$etudiant->prenom} {$etudiant->nom} a {$absence->total_heures} heures d'absence",
                    'color' => 'rose',
                    'icon' => 'user-clock',
                    'link' => route('secretariat.absences.index', ['etudiant' => $absence->etudiant_id])
                ];
            }
        }
        
        // Notes non saisies depuis longtemps
        $currentYear = AnneeAcademique::where('active', true)->first();
        if ($currentYear) {
            $matieresSansNotes = Matiere::whereDoesntHave('evaluations', function($q) {
                $q->whereYear('created_at', date('Y'));
            })->take(3)->get();
            
            foreach ($matieresSansNotes as $matiere) {
                $alerts[] = [
                    'message' => "Aucune note saisie pour la matière {$matiere->libelle}",
                    'color' => 'amber',
                    'icon' => 'exclamation-triangle',
                    'link' => route('secretariat.notes.index', ['matiere' => $matiere->id])
                ];
            }
        }
        
        return $alerts;
    }
    
    private function getRecentInscriptions()
    {
        $inscriptions = Inscription::with(['etudiant', 'classe'])
            ->latest()
            ->take(5)
            ->get();
        
        return $inscriptions->map(function($inscription) {
            return [
                'etudiant' => ($inscription->etudiant->prenom ?? '') . ' ' . ($inscription->etudiant->nom ?? ''),
                'classe' => $inscription->classe->nom ?? 'N/A',
                'date' => $inscription->created_at ? $inscription->created_at->format('d/m/Y') : 'N/A'
            ];
        });
    }
    
    private function getJuryStats()
    {
        $resultats = ResultatAnnuel::whereNotNull('decision')->get();
        
        return [
            'admis' => $resultats->where('decision', 'ADMIS')->count(),
            'redoublants' => $resultats->where('decision', 'REDOUBLEMENT')->count(),
            'exclus' => $resultats->where('decision', 'EXCLU')->count(),
        ];
    }
}