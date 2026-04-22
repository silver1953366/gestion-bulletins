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
    /**
     * Liste des étudiants avec pagination
     */
    public function index()
    {
        // On utilise la pagination pour éviter de charger 500 étudiants d'un coup
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
     * Enregistrement complet : Compte User + Fiche Etudiant + Profil
     */
    public function store(Request $request)
    {
        // 1. Validation stricte
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 2. Création de la fiche technique (Etudiant)
                $etudiant = Etudiant::create([
                    'nom' => strtoupper($request->nom), // On force le nom en majuscules (standard INPTIC)
                    'prenom' => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'lieu_naissance' => $request->lieu_naissance,
                    'bac' => $request->bac,
                    'provenance' => $request->provenance,
                ]);

                // 3. Création du compte de connexion (User)
                $user = User::create([
                    'first_name' => $request->prenom,
                    'last_name' => strtoupper($request->nom),
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id' => 4 // ID correspondant au rôle Étudiant dans ta base
                ]);

                // 4. Création du profil académique et génération du matricule
                StudentProfile::create([
                    'user_id' => $user->id,
                    'etudiant_id' => $etudiant->id,
                    'matricule' => 'INPTIC-' . date('Y') . '-' . str_pad($etudiant->id, 4, '0', STR_PAD_LEFT)
                ]);

                return redirect()->route('admin.etudiants.index')
                    ->with('success', "L'étudiant {$etudiant->nom} a été créé avec son compte utilisateur.");
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Fiche détaillée
     */
    public function show(Etudiant $etudiant)
    {
        // On charge les relations pour éviter les requêtes SQL en boucle (N+1)
        $etudiant->load(['studentProfile', 'inscriptions.classe']);

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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'bac' => 'nullable|string|max:100',
            'provenance' => 'nullable|string|max:255',
        ]);

        $etudiant->update($data);

        return redirect()->route('admin.etudiants.index')
            ->with('success', 'Fiche mise à jour avec succès.');
    }

    /**
     * Suppression (avec transaction pour la sécurité des données)
     */
    public function destroy(Etudiant $etudiant)
    {
        try {
            DB::transaction(function () use ($etudiant) {
                // Supprimer le profil et l'utilisateur lié si nécessaire
                if ($etudiant->studentProfile) {
                    $user = $etudiant->studentProfile->user;
                    $etudiant->studentProfile->delete();
                    if ($user) $user->delete();
                }
                $etudiant->delete();
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', 'Étudiant et compte utilisateur supprimés.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}