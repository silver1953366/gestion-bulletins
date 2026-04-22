<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherProfileController extends Controller
{
    /**
     * Affiche la liste des enseignants.
     */
    public function index()
    {
        // On charge 'user' et 'matieres' pour optimiser les performances (Eager Loading)
        $teachers = TeacherProfile::with(['user', 'matieres'])->paginate(10);
        
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Formulaire de création d'un nouveau profil.
     */
    public function create()
    {
        // On ne liste que les utilisateurs qui n'ont PAS encore de profil enseignant
        $users = User::whereDoesntHave('teacherProfile')->get();
        $matieres = Matiere::all();
        
        return view('admin.teachers.create', compact('users', 'matieres'));
    }

    /**
     * Enregistre un nouveau profil enseignant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|exists:users,id|unique:teacher_profiles,user_id',
            'specialite' => 'required|string|max:255',
            'grade'      => 'nullable|string|max:100',
            'matieres'   => 'nullable|array',
            'matieres.*' => 'exists:matieres,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Création du profil
                $teacher = TeacherProfile::create($request->only(['user_id', 'specialite', 'grade']));

                // Attachement des matières sélectionnées
                if ($request->filled('matieres')) {
                    $teacher->matieres()->attach($request->matieres);
                }
            });

            return redirect()->route('admin.teachers.index')
                             ->with('success', 'Le profil enseignant a été créé avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un enseignant spécifique.
     */
    public function show(TeacherProfile $teacher)
    {
        // On charge explicitement les relations pour la page de détails
        $teacher->load(['user', 'matieres']);
        
        return view('admin.teachers.show', compact('teacher'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(TeacherProfile $teacher)
    {
        // On charge les matières pour pouvoir les cocher dans la vue
        $matieres = Matiere::all();
        $teacher->load('matieres');

        return view('admin.teachers.edit', compact('teacher', 'matieres'));
    }

    /**
     * Met à jour le profil.
     */
    public function update(Request $request, TeacherProfile $teacher)
    {
        $request->validate([
            'specialite' => 'required|string|max:255',
            'grade'      => 'nullable|string|max:100',
            'matieres'   => 'nullable|array',
            'matieres.*' => 'exists:matieres,id'
        ]);

        try {
            DB::transaction(function () use ($request, $teacher) {
                // Mise à jour des infos de base
                $teacher->update($request->only(['specialite', 'grade']));

                // Synchronisation des matières (ajoute les nouvelles, supprime les anciennes)
                $teacher->matieres()->sync($request->matieres ?? []);
            });

            return redirect()->route('admin.teachers.index')
                             ->with('success', 'Le profil de ' . $teacher->user->name . ' a été mis à jour.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Supprime le profil (l'utilisateur reste en base, seul son profil enseignant disparaît).
     */
    public function destroy(TeacherProfile $teacher)
    {
        try {
            // Les matières liées seront détachées automatiquement si ta migration a "onDelete cascade" 
            // ou tu peux faire $teacher->matieres()->detach() avant si besoin.
            $teacher->delete();
            
            return redirect()->route('admin.teachers.index')
                             ->with('success', 'Profil enseignant supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce profil.');
        }
    }
}