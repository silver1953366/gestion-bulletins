<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Absence;
use App\Models\Bulletin;
use App\Models\User;
use App\Models\AnneeAcademique;
use App\Models\Matiere;
use App\Models\Ue;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur
     */
    public function index()
    {
        // 1. STATISTIQUES GLOBALES
        $stats = $this->getGlobalStatistics();
        
        // 2. PERFORMANCES ACADÉMIQUES
        $academicStats = $this->getAcademicStatistics();
        
        // 3. ÉTAT DES SOUTENANCES (S6)
        $soutenanceStats = $this->getSoutenanceStatistics();
        
        // 4. DÉCISIONS DE JURY
        $juryDecisions = $this->getJuryDecisions();
        
        // 5. ACTIVITÉS RÉCENTES (AUDIT)
        $recentActivities = $this->getRecentActivities();
        
        // 6. ALERTES ET ANOMALIES
        $alerts = $this->getAlerts();
        
        // 7. LISTE DES ÉTUDIANTS PAR SITUATION
        $studentsByStatus = $this->getStudentsByStatus();
        
        return view('admin.dashboard', compact(
            'stats',
            'academicStats',
            'soutenanceStats',
            'juryDecisions',
            'recentActivities',
            'alerts',
            'studentsByStatus'
        ));
    }

    /**
     * Statistiques globales de la plateforme
     */
    private function getGlobalStatistics()
    {
        $anneeActive = AnneeAcademique::where('active', true)->first();
        
        return [
            'total_etudiants' => Etudiant::count(),
            'total_enseignants' => User::whereHas('role', function($q) {
                $q->where('nom', 'enseignant');
            })->count(),
            'total_matieres' => Matiere::count(),
            'total_ues' => Ue::count(),
            'total_absences' => Absence::sum('heures'),
            'total_bulletins' => Bulletin::count(),
            'inscriptions_annee_active' => $anneeActive ? 
                \App\Models\Inscription::where('annee_academique_id', $anneeActive->id)->count() : 0,
            'taux_remplissage_notes' => $this->calculateTauxRemplissageNotes(),
        ];
    }

    /**
     * Statistiques académiques (moyennes, réussite)
     */
    private function getAcademicStatistics()
    {
        // Moyenne générale de la promotion
        $moyenneGenerale = ResultatAnnuel::avg('moyenne') ?? 0;
        
        // Répartition par mention
        $mentions = [
            'Passable (10-12)' => ResultatAnnuel::whereBetween('moyenne', [10, 11.99])->count(),
            'Assez Bien (12-14)' => ResultatAnnuel::whereBetween('moyenne', [12, 13.99])->count(),
            'Bien (14-16)' => ResultatAnnuel::whereBetween('moyenne', [14, 15.99])->count(),
            'Très Bien (16-20)' => ResultatAnnuel::where('moyenne', '>=', 16)->count(),
            'Non admis (<10)' => ResultatAnnuel::where('moyenne', '<', 10)->count(),
        ];
        
        // Taux de réussite global
        $totalEtudiantsAvecResultat = ResultatAnnuel::count();
        $totalReussite = ResultatAnnuel::where('decision', 'Diplômé(e)')->count();
        $tauxReussite = $totalEtudiantsAvecResultat > 0 
            ? round(($totalReussite / $totalEtudiantsAvecResultat) * 100, 2) 
            : 0;
        
        // Performance par semestre
        $moyenneS5 = ResultatSemestre::whereHas('semestre', function($q) {
            $q->where('libelle', 'S5');
        })->avg('moyenne') ?? 0;
        
        $moyenneS6 = ResultatSemestre::whereHas('semestre', function($q) {
            $q->where('libelle', 'S6');
        })->avg('moyenne') ?? 0;
        
        // Taux de validation des UE
        $uesValidees = DB::table('resultats_ues')->where('credits_acquis', '>', 0)->count();
        $totalUes = DB::table('resultats_ues')->count();
        $tauxValidationUE = $totalUes > 0 ? round(($uesValidees / $totalUes) * 100, 2) : 0;
        
        return [
            'moyenne_generale' => round($moyenneGenerale, 2),
            'mentions' => $mentions,
            'taux_reussite' => $tauxReussite,
            'moyenne_s5' => round($moyenneS5, 2),
            'moyenne_s6' => round($moyenneS6, 2),
            'taux_validation_ue' => $tauxValidationUE,
            'meilleure_moyenne' => ResultatAnnuel::max('moyenne') ?? 0,
            'moins_bonne_moyenne' => ResultatAnnuel::min('moyenne') ?? 0,
        ];
    }

    /**
     * Statistiques sur les soutenances (S6)
     */
    private function getSoutenanceStatistics()
    {
        // Récupérer l'UE6-2 (soutenance)
        $ueSoutenance = Ue::where('code', 'UE6-2')->orWhere('libelle', 'like', '%soutenance%')->first();
        
        if (!$ueSoutenance) {
            return [
                'total_inscrits' => 0,
                'soutenance_validee' => 0,
                'soutenance_non_validee' => 0,
                'en_attente_soutenance' => 0,
                'taux_reussite_soutenance' => 0,
            ];
        }
        
        $resultatsSoutenance = DB::table('resultats_ues')
            ->where('ue_id', $ueSoutenance->id)
            ->get();
        
        $total = $resultatsSoutenance->count();
        $validees = $resultatsSoutenance->filter(function($r) {
            return $r->credits_acquis > 0;
        })->count();
        
        return [
            'total_inscrits' => $total,
            'soutenance_validee' => $validees,
            'soutenance_non_validee' => $total - $validees,
            'en_attente_soutenance' => $this->countStudentsWaitingForSoutenance(),
            'taux_reussite_soutenance' => $total > 0 ? round(($validees / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Décisions du jury par catégorie
     */
    private function getJuryDecisions()
    {
        $decisions = [
            'Diplômé(e)' => ResultatAnnuel::where('decision', 'Diplômé(e)')->count(),
            'Reprise de soutenance' => ResultatAnnuel::where('decision', 'Reprise de soutenance')->count(),
            'Redouble la Licence 3' => ResultatAnnuel::where('decision', 'Redouble la Licence 3')->count(),
            'Non évalué' => ResultatAnnuel::whereNull('decision')->count(),
        ];
        
        // Top 5 des meilleurs étudiants
        $topStudents = ResultatAnnuel::with('etudiant')
            ->orderBy('moyenne', 'desc')
            ->take(5)
            ->get()
            ->map(function($resultat) {
                return [
                    'nom' => $resultat->etudiant->nom ?? 'N/A',
                    'prenom' => $resultat->etudiant->prenom ?? 'N/A',
                    'moyenne' => $resultat->moyenne,
                    'mention' => $resultat->mention,
                ];
            });
        
        // Étudiants à risque (moyenne < 10)
        $studentsAtRisk = ResultatAnnuel::with('etudiant')
            ->where('moyenne', '<', 10)
            ->whereNotNull('moyenne')
            ->count();
        
        return [
            'par_decision' => $decisions,
            'top_5' => $topStudents,
            'a_risque' => $studentsAtRisk,
        ];
    }

    /**
     * Activités récentes (journal d'audit)
     */
    private function getRecentActivities()
    {
        return DB::table('audit_logs')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->first_name . ' ' . $log->last_name,
                    'action' => $log->action,
                    'model' => $log->model,
                    'model_id' => $log->model_id,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at,
                ];
            });
    }

    /**
     * Alertes et anomalies
     */
    private function getAlerts()
    {
        $alerts = [];
        
        // 1. Étudiants sans notes
        $studentsWithoutNotes = Etudiant::whereDoesntHave('evaluations')->count();
        if ($studentsWithoutNotes > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$studentsWithoutNotes} étudiant(s) n'ont aucune note saisie.",
                'action' => route('admin.etudiants.sans-notes'),
            ];
        }
        
        // 2. Matières sans enseignant
        $matieresWithoutTeacher = Matiere::whereDoesntHave('enseignants')->count();
        if ($matieresWithoutTeacher > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$matieresWithoutTeacher} matière(s) sans enseignant assigné.",
                'action' => route('admin.matieres.sans-enseignant'),
            ];
        }
        
        // 3. Absences excessives
       $excessiveAbsences = Absence::selectRaw('etudiant_id, SUM(heures) as total_heures')
    ->groupBy('etudiant_id')
    ->having('total_heures', '>', 20)
    ->get();
        
        if ($excessiveAbsences->count() > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$excessiveAbsences->count()} étudiant(s) ont plus de 20 heures d'absence.",
                'action' => route('admin.absences.excessives'),
            ];
        }
        
        // 4. Semestre non validé mais année validée (incohérence)
        $inconsistentResults = DB::table('resultats_annuels as ra')
            ->leftJoin('resultats_semestres as rs', function($join) {
                $join->on('ra.etudiant_id', '=', 'rs.etudiant_id')
                     ->where('rs.valide', '=', 0);
            })
            ->where('ra.decision', 'Diplômé(e)')
            ->whereNotNull('rs.id')
            ->select('ra.etudiant_id')
            ->distinct()
            ->count();
        
        if ($inconsistentResults > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$inconsistentResults} incohérence(s) détectée(s) (diplômé mais semestre non validé).",
                'action' => route('admin.incohérences'),
            ];
        }
        
        // 5. Année académique active non définie
        $anneeActive = AnneeAcademique::where('active', true)->first();
        if (!$anneeActive) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Aucune année académique active n'est définie.",
                'action' => route('admin.annees-academiques'),
            ];
        }
        
        return $alerts;
    }

    /**
     * Liste des étudiants par situation académique
     */
    private function getStudentsByStatus()
    {
        return [
            'diplomes' => ResultatAnnuel::where('decision', 'Diplômé(e)')->count(),
            'reprise_soutenance' => ResultatAnnuel::where('decision', 'Reprise de soutenance')->count(),
            'redoublants' => ResultatAnnuel::where('decision', 'Redouble la Licence 3')->count(),
            'en_cours' => Etudiant::whereDoesntHave('resultatsAnnuel')->count(),
        ];
    }

    /**
     * Calcule le taux de remplissage des notes
     */
    private function calculateTauxRemplissageNotes()
    {
        $totalEvaluationsAttendues = Etudiant::count() * Matiere::count() * 2; // CC + Examen
        $totalEvaluationsSaisies = Evaluation::whereIn('type', ['CC', 'EXAMEN'])->count();
        
        if ($totalEvaluationsAttendues == 0) {
            return 0;
        }
        
        return round(($totalEvaluationsSaisies / $totalEvaluationsAttendues) * 100, 2);
    }

    /**
     * Compte les étudiants en attente de soutenance
     */
    private function countStudentsWaitingForSoutenance()
    {
        // Étudiants qui ont validé tous les crédits sauf l'UE6-2
        $ueSoutenance = Ue::where('code', 'UE6-2')->orWhere('libelle', 'like', '%soutenance%')->first();
        
        if (!$ueSoutenance) {
            return 0;
        }
        
        $etudiantsAyantValideSaufSoutenance = DB::table('resultats_ues as ru1')
            ->where('ru1.ue_id', '!=', $ueSoutenance->id)
            ->where('ru1.credits_acquis', '>', 0)
            ->select('ru1.etudiant_id')
            ->groupBy('ru1.etudiant_id')
            ->havingRaw('COUNT(DISTINCT ru1.ue_id) >= ?', [
                Ue::where('id', '!=', $ueSoutenance->id)->count()
            ])
            ->pluck('etudiant_id');
        
        $soutenanceNonValidee = DB::table('resultats_ues')
            ->where('ue_id', $ueSoutenance->id)
            ->where('credits_acquis', 0)
            ->pluck('etudiant_id');
        
        return $etudiantsAyantValideSaufSoutenance->intersect($soutenanceNonValidee)->count();
    }
}