<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnseignantMatiere;
use App\Models\TeacherProfile;
use App\Models\Matiere;
use Illuminate\Http\Request;

class EnseignantMatiereController extends Controller
{
    public function index()
    {
        // On récupère les affectations avec les relations nécessaires
        $affectations = EnseignantMatiere::with(['teacherProfile.user', 'matiere'])
            ->latest()
            ->paginate(15);
        
        // On récupère les profils enseignants avec le nom de l'utilisateur associé
        $teachers = TeacherProfile::with('user')->get()->sortBy(function($profile) {
            return $profile->user->name;
        });

        // Liste des matières triées par libellé
        $matieres = Matiere::orderBy('libelle')->get();

        return view('admin.enseignant-matiere.index', compact('affectations', 'teachers', 'matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_profile_id' => 'required|exists:teacher_profiles,id',
            'matiere_id'         => 'required|exists:matieres,id',
        ]);

        // Tentative de récupération ou création pour éviter les doublons proprement
        $affectation = EnseignantMatiere::firstOrCreate([
            'teacher_profile_id' => $request->teacher_profile_id,
            'matiere_id'         => $request->matiere_id,
        ]);

        if ($affectation->wasRecentlyCreated) {
            return redirect()->route('admin.enseignant-matiere.index')
                ->with('success', 'L\'enseignant a été affecté à la matière avec succès.');
        }

        return back()->with('error', 'Cet enseignant est déjà responsable de cette matière.');
    }

    public function destroy(EnseignantMatiere $enseignantMatiere)
    {
        // Suppression de la liaison
        $enseignantMatiere->delete();

        return back()->with('success', 'L\'attribution a été supprimée avec succès.');
    }
}