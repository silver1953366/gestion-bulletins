<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherProfileController extends Controller
{
    /**
     * Affiche la liste des profils et les comptes à compléter.
     */
    public function index()
    {
        // 1. Liste de tous les profils enseignants existants
        $teachers = TeacherProfile::with(['user', 'matieres'])
            ->latest()
            ->paginate(10);

        /**
         * 2. Récupération des utilisateurs "Enseignants" sans profil complet :
         * - Soit ils n'ont pas de TeacherProfile du tout.
         * - Soit leur spécialité est encore "À définir".
         */
        $availableUsers = User::where('role_id', 2) // ID 3 = Enseignant
            ->where(function($query) {
                $query->whereDoesntHave('teacherProfile')
                      ->orWhereHas('teacherProfile', function($q) {
                          $q->where('specialite', 'À définir')
                            ->orWhereNull('specialite');
                      });
            })
            ->orderBy('last_name')
            ->get();

        return view('admin.teachers.index', compact('teachers', 'availableUsers'));
    }

    /**
     * Finalise ou crée un profil enseignant (utilisé par le modal "Compléter").
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id',
            'specialite' => 'required|string|max:255',
            'grade'      => 'nullable|string|max:100',
        ]);

        try {
            // On utilise updateOrCreate pour gérer les deux cas (création ou finalisation)
            $teacher = TeacherProfile::updateOrCreate(
                ['user_id' => $request->user_id],
                [
                    'specialite' => $request->specialite,
                    'grade'      => $request->grade,
                ]
            );

            $userName = $teacher->user->full_name ?? 'de l\'enseignant';

            return redirect()->route('admin.teachers.index')
                ->with('success', "Le profil de {$userName} a été configuré avec succès.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la configuration : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations d'un profil existant (Bouton Modifier).
     */
    public function update(Request $request, TeacherProfile $teacher)
    {
        $request->validate([
            'specialite' => 'required|string|max:255',
            'grade'      => 'nullable|string|max:100',
        ]);

        try {
            $teacher->update($request->only(['specialite', 'grade']));

            $name = $teacher->user->full_name ?? 'de l\'enseignant';

            return redirect()->route('admin.teachers.index')
                ->with('success', "Le profil de {$name} a été mis à jour.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprime le profil (Détache les matières et supprime l'entrée profil).
     * Note: Le compte User reste intact.
     */
    public function destroy(TeacherProfile $teacher)
    {
        try {
            DB::transaction(function () use ($teacher) {
                // Nettoyage des attributions de matières
                $teacher->matieres()->detach();
                $teacher->delete();
            });

            return redirect()->route('admin.teachers.index')
                ->with('success', 'Le profil a été supprimé. Le compte utilisateur reste actif et peut être re-configuré.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Retourne les données JSON pour AlpineJS (Utile pour le modal de modification).
     */
    public function show(TeacherProfile $teacher)
    {
        return response()->json($teacher->load('user'));
    }
}