<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index()
    {
        $users = User::with('role')->latest()->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Stocke un nouvel utilisateur et initialise le profil si nécessaire.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:4',
            'role_id'    => 'required|exists:roles,id',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $result = DB::transaction(function () use ($request) {
                
                // 1. Gestion de la photo au moment de la création
                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('users-photos', 'public');
                }

                // 2. Création du compte utilisateur
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name'  => strtoupper($request->last_name),
                    'email'      => $request->email,
                    'password'   => Hash::make($request->password),
                    'role_id'    => $request->role_id,
                    'photo'      => $photoPath,
                ]);

                $etudiantId = null;

                // 3. Logique spécifique au rôle Étudiant (ID 4)
                if ($user->role_id == 4) {
                    $etudiant = Etudiant::create([
                        'nom'          => strtoupper($request->last_name),
                        'prenom'       => $request->first_name,
                        'is_finalized' => false
                    ]);

                    StudentProfile::create([
                        'user_id'     => $user->id,
                        'etudiant_id' => $etudiant->id,
                        'matricule'   => 'TEMP-' . str_pad($etudiant->id, 4, '0', STR_PAD_LEFT)
                    ]);

                    $etudiantId = $etudiant->id;
                }

                return ['user' => $user, 'etudiant_id' => $etudiantId];
            });

            // Redirection vers la finalisation si c'est un étudiant
            if ($result['etudiant_id']) {
                return redirect()->route('admin.etudiants.index', ['finalize' => $result['etudiant_id']])
                    ->with('success', "Compte créé. Veuillez finaliser le dossier académique de l'étudiant.");
            }

            return redirect()->route('admin.users.index')
                ->with('success', "Utilisateur {$result['user']->first_name} créé avec succès.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', "Erreur lors de la création : " . $e->getMessage());
        }
    }

    /**
     * Mise à jour de l'utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id'    => 'required|exists:roles,id',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'   => 'nullable|string|min:4|confirmed', 
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $user) {
                // Gestion de la photo (Update)
                if ($request->hasFile('photo')) {
                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }
                    $user->photo = $request->file('photo')->store('users-photos', 'public');
                }

                $user->first_name = $validated['first_name'];
                $user->last_name  = strtoupper($validated['last_name']);
                $user->email      = $validated['email'];
                $user->role_id    = $validated['role_id'];

                if (!empty($validated['password'])) {
                    $user->password = Hash::make($validated['password']);
                }

                $user->save();

                // Optionnel : Synchroniser avec la table etudiant si l'utilisateur est un étudiant
                if ($user->studentProfile && $user->studentProfile->etudiant) {
                    $user->studentProfile->etudiant->update([
                        'nom'    => strtoupper($validated['last_name']),
                        'prenom' => $validated['first_name'],
                    ]);
                }
            });

            return redirect()->route('admin.users.index')
                ->with('success', "Le profil de {$user->first_name} a été mis à jour.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur lors de la modification : " . $e->getMessage());
        }
    }

    /**
     * Suppression de l'utilisateur.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', "Action impossible : vous ne pouvez pas supprimer votre propre compte.");
        }

        try {
            DB::transaction(function () use ($user) {
                // 1. Suppression de la photo physique
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                // 2. Suppression des profils rattachés
                if ($user->studentProfile) {
                    // On peut aussi supprimer l'entrée dans la table 'etudiants' ici si nécessaire
                    if ($user->studentProfile->etudiant) {
                        $user->studentProfile->etudiant->delete();
                    }
                    $user->studentProfile->delete();
                }
                
                if ($user->teacherProfile) {
                    $user->teacherProfile()->delete();
                }
                
                // 3. Suppression finale de l'utilisateur
                $user->delete();
            });

            return redirect()->route('admin.users.index')
                ->with('success', "Utilisateur et données associées supprimés.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur de suppression : l'utilisateur possède peut-être des données protégées.");
        }
    }
}