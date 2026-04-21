<?php

namespace App\Http\Controllers;

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
        $teachers = TeacherProfile::with(['user', 'matieres'])->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        // On ne liste que les utilisateurs qui n'ont pas encore de profil enseignant
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
            'user_id' => 'required|exists:users,id|unique:teacher_profiles,user_id',
            'specialite' => 'required|string|max:255',
            'grade' => 'nullable|string|max:100',
            'matieres' => 'array' // IDs des matières enseignées
        ]);

        try {
            DB::transaction(function () use ($request) {
                $teacher = TeacherProfile::create($request->only(['user_id', 'specialite', 'grade']));

                // Attachement des matières (Table pivot enseignant_matiere)
                if ($request->has('matieres')) {
                    $teacher->matieres()->attach($request->matieres);
                }
            });

            return redirect()->route('admin.teachers.index')->with('success', 'Profil enseignant créé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(TeacherProfile $teacher)
    {
        $matieres = Matiere::all();
        return view('admin.teachers.edit', compact('teacher', 'matieres'));
    }

    /**
     * Met à jour le profil.
     */
    public function update(Request $request, TeacherProfile $teacher)
    {
        $request->validate([
            'specialite' => 'required|string|max:255',
            'grade' => 'nullable|string|max:100',
            'matieres' => 'array'
        ]);

        try {
            DB::transaction(function () use ($request, $teacher) {
                $teacher->update($request->only(['specialite', 'grade']));

                // Synchronisation des matières (ajoute les nouvelles, supprime les anciennes)
                $teacher->matieres()->sync($request->matieres);
            });

            return redirect()->route('admin.teachers.index')->with('success', 'Profil mis à jour.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Supprime le profil.
     */
    public function destroy(TeacherProfile $teacher)
    {
        $teacher->delete();
        return back()->with('success', 'Enseignant retiré de la liste.');
    }
}