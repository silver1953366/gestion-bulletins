<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
     * ÉTAPE 2 : Finalisation (Correction de l'erreur de doublon)
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
            // 1. Vérifier si l'étudiant a déjà un matricule définitif pour éviter le doublon
            $profile = $etudiant->studentProfile;
            
            if (!$profile || str_contains($profile->matricule, 'TEMP-')) {
                // Calcul du matricule : on cherche le dernier numéro attribué cette année
                $annee = date('Y');
                $lastStudent = StudentProfile::where('matricule', 'like', "INPTIC-$annee-%")
                    ->orderBy('matricule', 'desc')
                    ->first();

                if ($lastStudent) {
                    // On extrait le dernier nombre (ex: 003 de INPTIC-2026-003) et on fait +1
                    $lastNumber = (int) substr($lastStudent->matricule, -3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $matricule = "INPTIC-" . $annee . "-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            } else {
                // Si le matricule est déjà officiel, on garde l'existant
                $matricule = $profile->matricule;
            }

            // 2. Mise à jour de l'étudiant
            $etudiant->update([
                'date_naissance' => $request->date_naissance,
                'lieu_naissance' => strtoupper($request->lieu_naissance),
                'bac'            => strtoupper($request->bac),
                'provenance'     => $request->provenance,
                'is_finalized'   => true,
            ]);

            // 3. Mise à jour du profil
            $etudiant->studentProfile()->update([
                'matricule' => $matricule
            ]);

            DB::commit();

            return redirect()->route('admin.etudiants.index')
                ->with('finalized_success', true)
                ->with('matricule', $matricule);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur de finalisation : ' . $e->getMessage());
        }
    }

    /**
     * Mise à jour du dossier (Photo uniquement via User)
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
                
                // Gestion de la photo dans la table USERS
                if ($request->hasFile('photo') && $user) {
                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }
                    $photoPath = $request->file('photo')->store('users-photos', 'public');
                    $user->update(['photo' => $photoPath]);
                }

                // Mise à jour Etudiant
                $etudiant->update([
                    'nom'            => strtoupper($request->nom),
                    'prenom'         => $request->prenom,
                    'date_naissance' => $request->date_naissance,
                    'lieu_naissance' => strtoupper($request->lieu_naissance),
                    'bac'            => strtoupper($request->bac),
                    'provenance'     => $request->provenance,
                ]);

                // Synchronisation User
                if ($user) {
                    $user->update([
                        'first_name' => $request->prenom,
                        'last_name'  => strtoupper($request->nom),
                        'email'      => $request->email,
                    ]);
                }
            });

            return redirect()->route('admin.etudiants.index')
                ->with('success', "Le dossier de l'étudiant " . $etudiant->nom . " a été mis à jour.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur de mise à jour : " . $e->getMessage());
        }
    }

    /**
     * Suppression (Cascade manuelle)
     */
    public function destroy(Etudiant $etudiant)
    {
        try {
            DB::transaction(function () use ($etudiant) {
                if ($etudiant->studentProfile) {
                    $user = $etudiant->studentProfile->user;
                    if ($user && $user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }
                    $etudiant->studentProfile->delete();
                    if ($user) { $user->delete(); }
                }
                $etudiant->delete();
            });
            return redirect()->route('admin.etudiants.index')->with('success', "Suppression réussie.");
        } catch (\Exception $e) {
            return back()->with('error', "Échec de la suppression.");
        }
    }
}