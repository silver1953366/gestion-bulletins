<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Matiere;
use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Affiche le formulaire de saisie des notes pour une classe et une matière.
     * On récupère les étudiants avec leurs notes existantes pour permettre la modification.
     */
    public function formulaireSaisie(Request $request)
    {
        // Validation des paramètres de filtrage
        $request->validate([
            'matiere_id' => 'required|exists:matieres,id',
            'classe_id'  => 'required|exists:classes,id',
        ]);

        $matiere = Matiere::findOrFail($request->matiere_id);
        $classe = Classe::findOrFail($request->classe_id);

        // Récupération des étudiants de la classe avec leurs évaluations pour cette matière
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
     * Enregistre ou met à jour une note individuelle.
     * Exigence 5.2 : Contrôle de cohérence (0-20).
     * Exigence 8.2 : Traçabilité (created_by).
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
            'type.in'      => 'Le type d\'évaluation est invalide.',
        ]);

        // Mise à jour si la combinaison (étudiant, matière, type) existe, sinon création
        $evaluation = Evaluation::updateOrCreate(
            [
                'etudiant_id' => $request->etudiant_id,
                'matiere_id'  => $request->matiere_id,
                'type'        => $request->type,
            ],
            [
                'note'       => $request->note,
                'created_by' => Auth::id(), // Enregistre qui a fait la saisie
            ]
        );

        // Exigence 5.3 : Recalcul automatique de la moyenne de l'étudiant pour cette matière
        $this->actualiserMoyenneMatiere($request->etudiant_id, $request->matiere_id);

        return back()->with('success', 'Note enregistrée avec succès.');
    }

    /**
     * Logique interne pour déclencher le calcul de la moyenne.
     * Cette méthode fait le pont avec le ResultatMatiereController.
     */
    protected function actualiserMoyenneMatiere($etudiantId, $matiereId)
    {
        try {
            // On instancie le contrôleur de calcul pour mettre à jour la table des résultats
            $calculateur = new ResultatMatiereController();
            $calculateur->calculerMoyenne($etudiantId, $matiereId);
        } catch (\Exception $e) {
            // On ne bloque pas l'utilisateur si le calculateur de moyenne échoue, 
            // mais on pourrait logger l'erreur ici.
        }
    }

    /**
     * Suppression d'une note (optionnel)
     */
    public function destroy(Evaluation $evaluation)
    {
        $etudiantId = $evaluation->etudiant_id;
        $matiereId = $evaluation->matiere_id;

        $evaluation->delete();

        // Recalcul après suppression
        $this->actualiserMoyenneMatiere($etudiantId, $matiereId);

        return back()->with('success', 'Note supprimée.');
    }
}