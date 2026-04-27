<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class EtudiantController extends Controller
{
    /**
     * Affiche l'annuaire complet.
     */
    public function index()
    {
        $etudiants = Etudiant::with(['studentProfile.user'])->latest()->paginate(10);
        return view('admin.etudiants.index', compact('etudiants'));
    }

    /**
     * ÉTAPE 1 : Création initiale (Compte User + Profil de base)
     * C'est ici qu'on déclenche la redirection vers la finalisation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'    => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email'  => 'required|email|unique:users,email',
        ]);

        try {
            $etudiant = DB::transaction(function () use ($request) {
                // 1. Créer l'utilisateur
                $user = User::create([
                    'first_name' => $request->prenom,
                    'last_name'  => strtoupper($request->nom),
                    'email'      => $request->email,
                    'password'   => Hash::make('Etudiant2026'), // Mot de passe par défaut
                ]);

                // 2. Créer l'étudiant
                $etudiant = Etudiant::create([
                    'nom'          => strtoupper($request->nom),
                    'prenom'       => $request->prenom,
                    'is_finalized' => false,
                ]);

                // 3. Créer le profil étudiant (Pivot) avec matricule temporaire
                StudentProfile::create([
                    'user_id'     => $user->id,
                    'etudiant_id' => $etudiant->id,
                    'matricule'   => 'TEMP-' . strtoupper(Str::random(6)),
                ]);

                return $etudiant;
            });

            // Redirection vers l'index avec les instructions pour Alpine.js
            return redirect()->route('admin.etudiants.index')
                ->with('success', "Le compte de {$etudiant->prenom} a été créé.")
                ->with('open_finalize_modal', true)
                ->with('etudiant_to_finalize', $etudiant->load('studentProfile.user'));

        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la création : " . $e->getMessage());
        }
    }

    /**
     * ÉTAPE 2 : Finalisation du dossier académique.
     * Génère un matricule définitif et complète les infos.
     */
    public function finalize(Request $request, $id)
    {
        $etudiant = Etudiant::findOrFail($id);

        $request->validate([
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:100',
            'bac'            => 'required|string|max:50',
            'provenance'     => 'required|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $profile = $etudiant->studentProfile;
            
            // Logique de génération de matricule INPTIC-YYYY-XXX
            if (!$profile || str_contains($profile->matricule, 'TEMP-')) {
                $annee = date('Y');
                
                $lastStudent = StudentProfile::where('matricule', 'like', "INPTIC-$annee-%")
                    ->orderBy('matricule', 'desc')
                    ->first();

                if ($lastStudent) {
                    $lastNumber = (int) substr($lastStudent->matricule, -3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $matricule = "INPTIC-" . $annee . "-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $matricule = $profile->matricule;
            }

            // Mise à jour de l'étudiant
            $etudiant->update([
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => strtoupper($request->lieu_naissance),
                'bac'            => strtoupper($request->bac),
                'provenance'     => $request->provenance,
                'is_finalized'   => true,
            ]);

            // Mise à jour du matricule officiel
            if ($profile) {
                $profile->update(['matricule' => $matricule]);
            }

            DB::commit();

            return redirect()->route('admin.etudiants.index')
                ->with('success', "Le dossier de {$etudiant->prenom} est désormais finalisé.")
                ->with('finalized_success', true)
                ->with('matricule', $matricule);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur de finalisation : ' . $e->getMessage());
        }
    }

    /**
     * Mise à jour classique du dossier (Identité, Email et Photo).
     */
    public function update(Request $request, Etudiant $etudiant)
    {
        $user = $etudiant->studentProfile->user ?? null;

        $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => ['required', 'email', Rule::unique('users')->ignore($user?->id)],
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'bac'            => 'nullable|string|max:50',
            'provenance'     => 'nullable|string|max:100',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $etudiant, $user) {
                
                // Gestion de la photo
                if ($request->hasFile('photo') && $user) {
                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }
                    $photoPath = $request->file('photo')->store('users-photos', 'public');
                    $user->update(['photo' => $photoPath]);
                }

                // Mise à jour table etudiants
                $etudiant->update([
                    'nom'            => strtoupper($request->nom),
                    'prenom'         => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'lieu_naissance' => strtoupper($request->lieu_naissance),
                    'bac'            => strtoupper($request->bac),
                    'provenance'     => $request->provenance,
                ]);

                // Mise à jour table users
                if ($user) {
                    $user->update([
                        'first_name' => $request->prenom,
                        'last_name'  => strtoupper($request->nom),
                        'email'      => $request->email,
                    ]);
                }
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', "Le dossier a été mis à jour.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur de mise à jour : " . $e->getMessage());
        }
    }

    /**
     * Suppression en cascade.
     */
    public function destroy(Etudiant $etudiant)
    {
        try {
            DB::transaction(function () use ($etudiant) {
                if ($etudiant->studentProfile) {
                    $user = $etudiant->studentProfile->user;
                    
                    if ($user) {
                        if ($user->photo) {
                            Storage::disk('public')->delete($user->photo);
                        }
                        $user->delete(); // Supprime l'user et le profile via cascade
                    }
                }
                $etudiant->delete();
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', "Dossier supprimé définitivement.");

        } catch (\Exception $e) {
            return back()->with('error', "Échec de la suppression.");
        }
    }
}