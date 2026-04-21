<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Absence;
use App\Models\Bulletin;
use App\Models\parametre;
use App\Models\User;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Matiere;
use App\Models\Ue;
use App\Models\ResultatSemestre;
use App\Models\ResultatAnnuel;
use App\Models\AuditLog;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur avec une vision 360°
     */
    public function index()
    {
        // 1. STATISTIQUES GLOBALES
        $stats = $this->getGlobalStatistics();
        
        // 2. PERFORMANCES ACADÉMIQUES
        $academicStats = $this->getAcademicStatistics();
        
        // 3. ÉTAT DES SOUTENANCES (Spécifique S6 / UE6-2)
        $soutenanceStats = $this->getSoutenanceStatistics();
        
        // 4. DÉCISIONS DE JURY & TOP ÉTUDIANTS
        $juryDecisions = $this->getJuryDecisions();
        
        // 5. ACTIVITÉS RÉCENTES
        $recentActivities = $this->getRecentActivities();
        
        // 6. ALERTES INTELLIGENTES
        $alerts = $this->getAlerts();
        
        // 7. SITUATION ACADÉMIQUE GLOBALE
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
     * Statistiques globales incluant le taux de saisie des notes
     */
    private function getGlobalStatistics()
    {
        return [
            'total_etudiants' => Etudiant::count(),
            'total_enseignants' => User::whereHas('role', function($q) {
                $q->where('nom', 'enseignant');
            })->count(),
            'total_matieres' => Matiere::count(),
            'total_ues' => Ue::count(),
            'total_absences' => Absence::sum('heures') ?? 0,
            'total_bulletins' => Bulletin::count(),
            'total_filieres' => Filiere::count(),
            'taux_remplissage_notes' => $this->calculateTauxRemplissageNotes(),
        ];
    }

    /**
     * Analyse des performances : Moyennes et Mentions
     */
    private function getAcademicStatistics()
    {
        $moyenneGenerale = ResultatAnnuel::avg('moyenne') ?? 0;
        
        $mentions = [
            'Passable'   => ResultatAnnuel::whereBetween('moyenne', [10, 11.99])->count(),
            'Assez Bien' => ResultatAnnuel::whereBetween('moyenne', [12, 13.99])->count(),
            'Bien'       => ResultatAnnuel::whereBetween('moyenne', [14, 15.99])->count(),
            'Très Bien'  => ResultatAnnuel::where('moyenne', '>=', 16)->count(),
            'Échec'      => ResultatAnnuel::where('moyenne', '<', 10)->count(),
        ];
        
        $totalRes = ResultatAnnuel::count();
        $totalReussite = ResultatAnnuel::where('decision', 'Diplômé(e)')->count();
        
        return [
            'moyenne_generale' => round($moyenneGenerale, 2),
            'mentions'         => $mentions,
            'taux_reussite'    => $totalRes > 0 ? round(($totalReussite / $totalRes) * 100, 2) : 0,
            'moyenne_s5'       => round(ResultatSemestre::whereHas('semestre', fn($q) => $q->where('libelle', 'S5'))->avg('moyenne') ?? 0, 2),
            'moyenne_s6'       => round(ResultatSemestre::whereHas('semestre', fn($q) => $q->where('libelle', 'S6'))->avg('moyenne') ?? 0, 2),
            'meilleure_moyenne'=> ResultatAnnuel::max('moyenne') ?? 0,
        ];
    }

    /**
     * Focus sur les soutenances (UE6-2)
     */
    private function getSoutenanceStatistics()
    {
        $ueSoutenance = Ue::where('code', 'UE6-2')
                          ->orWhere('libelle', 'like', '%soutenance%')
                          ->first();
        
        // Initialisation par défaut pour éviter l'erreur "Undefined array key"
        $default = [
            'total_inscrits'           => 0,
            'soutenance_validee'       => 0,
            'en_attente_soutenance'    => 0,
            'taux_reussite_soutenance' => 0,
        ];

        if (!$ueSoutenance) return $default;
        
        $stats = DB::table('resultats_ues')->where('ue_id', $ueSoutenance->id);
        $total = $stats->count();
        $validees = $stats->where('credits_acquis', '>', 0)->count();

        return [
            'total_inscrits'           => $total,
            'soutenance_validee'       => $validees,
            'en_attente_soutenance'    => $this->countStudentsWaitingForSoutenance($ueSoutenance->id),
            'taux_reussite_soutenance' => $total > 0 ? round(($validees / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Décisions du jury et Top 5
     */
    private function getJuryDecisions()
    {
        return [
            'par_decision' => [
                'Diplômé(e)'            => ResultatAnnuel::where('decision', 'Diplômé(e)')->count(),
                'Reprise de soutenance' => ResultatAnnuel::where('decision', 'Reprise de soutenance')->count(),
                'Redoublants'           => ResultatAnnuel::where('decision', 'Redouble la Licence 3')->count(),
            ],
            'top_5'    => ResultatAnnuel::with('etudiant')->orderByDesc('moyenne')->take(5)->get(),
            'a_risque' => ResultatAnnuel::where('moyenne', '<', 10)->count(),
        ];
    }

    /**
     * Journal d'Audit (Activités récentes)
     */
    private function getRecentActivities()
    {
        return AuditLog::with('user')->latest()->take(15)->get();
    }

    /**
     * Système d'alertes pour incohérences de données
     */
    private function getAlerts()
    {
        $alerts = [];
        
        // 1. Étudiants sans aucune note
        if (($count = Etudiant::whereDoesntHave('evaluations')->count()) > 0) {
            $alerts[] = ['type' => 'warning', 'message' => "$count étudiant(s) sans notes.", 'icon' => 'fas fa-user-slash'];
        }
        
        // 2. Matières orphelines
        if (($count = Matiere::whereDoesntHave('enseignants')->count()) > 0) {
            $alerts[] = ['type' => 'danger', 'message' => "$count matière(s) sans enseignant.", 'icon' => 'fas fa-book-dead'];
        }
        
        // 3. Absences critiques (> 20h)
        $excessive = DB::table('absences')->select('etudiant_id')->groupBy('etudiant_id')->havingRaw('SUM(heures) > 20')->count();
        if ($excessive > 0) {
            $alerts[] = ['type' => 'info', 'message' => "$excessive étudiant(s) dépassent 20h d'absence.", 'icon' => 'fas fa-clock'];
        }

        // 4. Vérification Année Académique
        if (!AnneeAcademique::where('active', true)->exists()) {
            $alerts[] = ['type' => 'danger', 'message' => "Attention : Aucune année académique active !", 'icon' => 'fas fa-exclamation-triangle'];
        }
        
        return $alerts;
    }

    private function getStudentsByStatus()
    {
        return [
            'diplomes'    => ResultatAnnuel::where('decision', 'Diplômé(e)')->count(),
            'reprise'     => ResultatAnnuel::where('decision', 'Reprise de soutenance')->count(),
            'redoublants' => ResultatAnnuel::where('decision', 'Redouble la Licence 3')->count(),
            'en_attente'  => Etudiant::whereDoesntHave('resultatsAnnuel')->count(),
        ];
    }

    private function calculateTauxRemplissageNotes()
    {
        $etudiantsCount = Etudiant::count();
        $matieresCount = Matiere::count();
        if ($etudiantsCount == 0 || $matieresCount == 0) return 0;

        $totalAttendu = $etudiantsCount * $matieresCount * 2; // CC + EXAMEN
        $totalSaisi = Evaluation::whereIn('type', ['CC', 'EXAMEN'])->count();
        
        return round(($totalSaisi / $totalAttendu) * 100, 2);
    }

    private function countStudentsWaitingForSoutenance($ueSoutenanceId)
    {
        $totalUesSaufSoutenance = Ue::where('id', '!=', $ueSoutenanceId)->count();
        if ($totalUesSaufSoutenance == 0) return 0;

        return DB::table('resultats_ues as ru')
            ->select('etudiant_id')
            ->where('ue_id', '!=', $ueSoutenanceId)
            ->where('credits_acquis', '>', 0)
            ->groupBy('etudiant_id')
            ->havingRaw('COUNT(DISTINCT ue_id) >= ?', [$totalUesSaufSoutenance])
            ->whereIn('etudiant_id', function($query) use ($ueSoutenanceId) {
                $query->select('etudiant_id')->from('resultats_ues')
                      ->where('ue_id', $ueSoutenanceId)->where('credits_acquis', 0);
            })->count();
    }
}