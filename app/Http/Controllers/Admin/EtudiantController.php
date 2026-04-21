<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use App\Models\Inscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EtudiantController extends Controller
{ 

public function store(Request $request)
{
<<<<<<< HEAD
    // 1. Validation
    $request->validate([
        'nom' => 'required',
        'prenom' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);

    // 2. Création étudiant
    $etudiant = Etudiant::create([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'date_naissance' => $request->date_naissance,
        'lieu_naissance' => $request->lieu_naissance,
        'bac' => $request->bac,
        'provenance' => $request->provenance,
    ]);

    // 3. Création user
    $user = User::create([
        'first_name' => $request->prenom,
        'last_name' => $request->nom,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => 4 // étudiant
    ]);

    // 4. Lien profile
    StudentProfile::create([
        'user_id' => $user->id,
        'etudiant_id' => $etudiant->id,
        'matricule' => 'ETU' . str_pad($etudiant->id, 4, '0', STR_PAD_LEFT)
    ]);

    return redirect()->route('admin.etudiants.index')
        ->with('success', 'Étudiant créé avec succès');
}
public function create()
{
    return view('admin.etudiants.create');
}

public function index()
{
    $etudiants = Etudiant::latest()->get();

    return view('admin.etudiants.index', compact('etudiants'));
}


public function show($id)
{
    $etudiant = Etudiant::findOrFail($id);

    $profil = StudentProfile::where('etudiant_id', $id)->first();

    $inscriptions = Inscription::with('classe', 'anneeAcademique')
        ->where('etudiant_id', $id)
        ->get();

    return view('admin.etudiants.show', compact(
        'etudiant',
        'profil',
        'inscriptions'
    ));
}
=======
    /**
     * Liste des étudiants avec pagination
     */
    public function index()
    {
        $etudiants = Etudiant::latest()->paginate(10);
        
        return view('admin.etudiants.index', compact('etudiants'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.etudiants.create');
    }

    /**
     * Enregistrement d'un nouvel étudiant
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'bac'            => 'nullable|string|max:100',
            'provenance'     => 'nullable|string|max:255',
        ]);

        try {
            Etudiant::create($data);
            return redirect()
                ->route('admin.etudiants.index')
                ->with('success', 'L\'étudiant a été inscrit avec succès dans la promotion LP ASUR.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
        }
    }

    /**
     * Fiche détaillée de l'étudiant (Profil, Notes, Absences)
     */
    public function show(Etudiant $etudiant)
    {
        // On charge les relations nécessaires pour afficher le bilan sur la fiche
        $etudiant->load([
            'studentProfile', 
            'inscriptions.classe', 
            'absences.matiere',
            'resultatsSemestres'
        ]);

        return view('admin.etudiants.show', compact('etudiant'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Etudiant $etudiant)
    {
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    /**
     * Mise à jour des informations
     */
    public function update(Request $request, Etudiant $etudiant)
    {
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'bac'            => 'nullable|string|max:100',
            'provenance'     => 'nullable|string|max:255',
        ]);

        try {
            $etudiant->update($data);
            return redirect()
                ->route('admin.etudiants.index')
                ->with('success', 'La fiche de ' . $etudiant->full_name . ' a été mise à jour.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification.');
        }
    }

    /**
     * Suppression d'un étudiant et de ses données liées
     */
    public function destroy(Etudiant $etudiant)
    {
        try {
            // Utilisation d'une transaction pour s'assurer que tout est supprimé proprement
            DB::transaction(function () use ($etudiant) {
                // Si tu n'as pas configuré de "onDelete cascade" dans tes migrations :
                $etudiant->evaluations()->delete();
                $etudiant->absences()->delete();
                $etudiant->delete();
            });

            return redirect()
                ->route('admin.etudiants.index')
                ->with('success', 'Étudiant supprimé de la base de données.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet étudiant (des données y sont liées).');
        }
    }
>>>>>>> 6f3d284 (Initialisation ERP INPTIC : Sidebar et Layout fonctionnels)
}