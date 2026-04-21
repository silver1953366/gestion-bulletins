<?php

namespace App\Http\Controllers;

use App\Models\StudentProfile;
use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentProfileController extends Controller
{
    /**
     * Affiche la liste des profils étudiants liés.
     */
    public function index()
    {
        $profiles = StudentProfile::with(['user', 'etudiant'])->paginate(15);
        return view('admin.student_profiles.index', compact('profiles'));
    }

    /**
     * Lie un utilisateur à un étudiant et lui assigne un matricule.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:student_profiles,user_id',
            'etudiant_id' => 'required|exists:etudiants,id|unique:student_profiles,etudiant_id',
            'matricule' => 'required|string|unique:student_profiles,matricule|max:50',
        ]);

        try {
            StudentProfile::create($request->all());
            return back()->with('success', 'Le matricule a été généré et le compte lié avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la liaison : ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le matricule ou change l'utilisateur lié.
     */
    public function update(Request $request, StudentProfile $studentProfile)
    {
        $request->validate([
            'matricule' => 'required|string|unique:student_profiles,matricule,' . $studentProfile->id,
            'user_id' => 'required|exists:users,id|unique:student_profiles,user_id,' . $studentProfile->id,
        ]);

        $studentProfile->update($request->all());

        return back()->with('success', 'Profil étudiant mis à jour.');
    }

    /**
     * Supprime la liaison (ne supprime pas l'étudiant ni l'user).
     */
    public function destroy(StudentProfile $studentProfile)
    {
        $studentProfile->delete();
        return back()->with('success', 'Liaison rompue (le compte et la fiche existent toujours).');
    }
}