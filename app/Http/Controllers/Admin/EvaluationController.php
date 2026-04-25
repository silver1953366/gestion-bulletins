<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\ResultatMatiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Page initiale : Sélection de la classe et de la matière.
     * C'est ici que renvoie le lien "Saisie des Notes" de la sidebar.
     */
    public function index()
    {
        $classes = Classe::orderBy('nom')->get();
        $matieres = Matiere::orderBy('libelle')->get();
        
        return view('admin.evaluations.index', compact('classes', 'matieres'));
    }

    /**
     * Affiche le formulaire de saisie pour une classe et une matière spécifique.
     */
    public function formulaireSaisie(Request $request)
    {
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id'  => 'required|exists:classes,id',
        ]);

        $matiere = Matiere::findOrFail($request->matiere_id);
        $classe = Classe::findOrFail($request->classe_id);

        // Récupération des étudiants inscrits dans cette classe
        // On charge les évaluations pour éviter les requêtes N+1 dans la vue
        $etudiants = Etudiant::whereHas('inscriptions', function($q) use ($classe) {
                $q->where('classe_id', $classe->id);
            })
            ->with(['evaluations' => function($q) use ($matiere) {
                $q->where('matiere_id', $matiere->id);
            }])
            ->get()
            ->sortBy('nom');

        return view('admin.evaluations.saisie', compact('matiere', 'classe', 'etudiants'));
    }

    /**
     * Enregistre ou met à jour une note.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id'  => 'required|exists:matieres,id',
            'type'        => 'required|in:CC,EXAMEN,RATTRAPAGE',
            'note'        => 'required|numeric|between:0,20',
        ], [
            'note.between' => 'La note doit être comprise entre 0 et 20.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Sauvegarde de la note
            $evaluation = Evaluation::updateOrCreate(
                [
                    'etudiant_id' => $request->etudiant_id,
                    'matiere_id'  => $request->matiere_id,
                    'type'        => $request->type,
                ],
                [
                    'note'       => $request->note,
                    'created_by' => Auth::id(),
                ]
            );

            // 2. Recalcul automatique de la moyenne pour cette matière
            $this->actualiserMoyenneMatiere($request->etudiant_id, $request->matiere_id);

            DB::commit();
            return back()->with('success', 'Note de ' . $request->type . ' enregistrée.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    /**
     * Calcule la moyenne pondérée (40% CC / 60% EXAM) et gère le rattrapage.
     */
    protected function actualiserMoyenneMatiere($etudiantId, $matiereId)
    {
        // Récupérer toutes les notes de l'étudiant pour cette matière
        $notes = Evaluation::where('etudiant_id', $etudiantId)
            ->where('matiere_id', $matiereId)
            ->get()
            ->pluck('note', 'type');

        $cc = $notes->get('CC', 0);
        $examen = $notes->get('EXAMEN', 0);
        $rattrapage = $notes->get('RATTRAPAGE', null);

        // Logique INPTIC : Le rattrapage remplace l'examen s'il est meilleur
        $noteFinaleExamen = $examen;
        if ($rattrapage !== null && $rattrapage > $examen) {
            $noteFinaleExamen = $rattrapage;
        }

        // Calcul de la moyenne (Pondération standard LMD)
        $moyenne = ($cc * 0.4) + ($noteFinaleExamen * 0.6);

        // Mise à jour de la table des résultats
        ResultatMatiere::updateOrCreate(
            [
                'etudiant_id' => $etudiantId,
                'matiere_id'  => $matiereId,
            ],
            [
                'moyenne' => $moyenne,
                'valide'  => $moyenne >= 10,
            ]
        );
    }

    /**
     * Supprime une note.
     */
    public function destroy(Evaluation $evaluation)
    {
        $etudiantId = $evaluation->etudiant_id;
        $matiereId = $evaluation->matiere_id;

        $evaluation->delete();

        // On recalcule la moyenne après suppression
        $this->actualiserMoyenneMatiere($etudiantId, $matiereId);

        return back()->with('success', 'Note supprimée et moyenne actualisée.');
    }
}