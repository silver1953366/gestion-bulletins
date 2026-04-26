<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EtudiantController extends Controller
{
    /**
     * Affiche la liste des étudiants avec pagination.
     */
    public function index()
    {
        // On charge les relations 'studentProfile.user' pour éviter le problème de requêtes N+1
        // Cela permet d'afficher le matricule et l'email dans la liste sans ralentir le serveur.
        $etudiants = Etudiant::with('studentProfile.user')->latest()->paginate(10);
        
        return view('admin.etudiants.index', compact('etudiants'));
    }

    /**
     * Affiche le formulaire de création d'un étudiant.
     */
    public function create()
    {
        return view('admin.etudiants.create');
    }

    /**
     * Enregistre un étudiant : crée la fiche Etudiant, le User et le StudentProfile.
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:6',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
            'bac'            => 'nullable|string|max:100',
            'provenance'     => 'nullable|string|max:255',
        ]);

        try {
            // Utilisation d'une transaction pour garantir l'intégrité des données
            return DB::transaction(function () use ($request) {
                
                // 2. Création de la fiche "Métier" (Table etudiants)
                $etudiant = Etudiant::create([
                    'nom'            => strtoupper($request->nom), // Norme : Nom en majuscules
                    'prenom'         => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'lieu_naissance' => $request->lieu_naissance,
                    'bac'            => $request->bac,
                    'provenance'     => $request->provenance,
                ]);

                // 3. Création du compte de connexion (Table users)
                $user = User::create([
                    'first_name' => $request->prenom,
                    'last_name'  => strtoupper($request->nom),
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'role_id'    => 4, // ID supposé du rôle 'Étudiant'
                ]);

                // 4. Création du profil académique (Table student_profiles)
                // C'est ici qu'on génère le matricule unique
                StudentProfile::create([
                    'user_id'     => $user->id,
                    'etudiant_id' => $etudiant->id,
                    'matricule'   => 'INPTIC-' . date('Y') . '-' . str_pad($etudiant->id, 4, '0', STR_PAD_LEFT),
                ]);

                return redirect()->route('admin.etudiants.index')
                    ->with('success', "L'étudiant {$etudiant->nom} a été enregistré avec son compte utilisateur.");
            });

        } catch (\Exception $e) {
            // En cas d'erreur, on revient en arrière avec les saisies
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un étudiant (Fiche, Profil, Inscriptions).
     */
    public function show(Etudiant $etudiant)
    {
        // On charge les relations nécessaires pour la fiche détaillée
        $etudiant->load(['studentProfile.user', 'inscriptions.classe']);

        return view('admin.etudiants.show', compact('etudiant'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Etudiant $etudiant)
    {
        // On charge le user lié pour pouvoir modifier l'email dans le formulaire
        $etudiant->load('studentProfile.user');
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    /**
     * Met à jour les informations de l'étudiant et de son compte utilisateur.
     */
    public function update(Request $request, Etudiant $etudiant)
    {
        // Récupération de l'utilisateur associé via le profil
        $user = $etudiant->studentProfile->user;

        // Validation (on ignore l'ID actuel pour l'unicité de l'email)
        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'date_naissance' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($request, $etudiant, $user) {
                // Mise à jour de la fiche étudiant
                $etudiant->update([
                    'nom'            => strtoupper($request->nom),
                    'prenom'         => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'lieu_naissance' => $request->lieu_naissance,
                    'bac'            => $request->bac,
                    'provenance'     => $request->provenance,
                ]);

                // Mise à jour synchronisée du compte utilisateur
                $user->update([
                    'first_name' => $request->prenom,
                    'last_name'  => strtoupper($request->nom),
                    'email'      => $request->email,
                ]);
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', "Les informations de l'étudiant ont été mises à jour.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Supprime l'étudiant, son profil et son compte utilisateur.
     */
    public function destroy(Etudiant $etudiant)
    {
        try {
            DB::transaction(function () use ($etudiant) {
                if ($etudiant->studentProfile) {
                    $user = $etudiant->studentProfile->user;
                    
                    // 1. On supprime d'abord le profil (table pivot/liaison)
                    $etudiant->studentProfile->delete();
                    
                    // 2. On supprime le compte de connexion
                    if ($user) {
                        $user->delete();
                    }
                }

                // 3. Enfin, on supprime la fiche étudiant
                $etudiant->delete();
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', 'Étudiant et compte associé supprimés avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}