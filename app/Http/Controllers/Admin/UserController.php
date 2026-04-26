<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
     * Stocke un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:4|confirmed',
            'role_id'    => 'required|exists:roles,id',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $user = DB::transaction(function () use ($request, $validated) {
                $path = null;
                if ($request->hasFile('photo')) {
                    $path = $request->file('photo')->store('users-photos', 'public');
                }

                // IMPORTANT : On passe le mot de passe en clair car le Model User 
                // s'occupe du Hash via le cast 'hashed' défini dans ton modèle.
                return User::create([
                    'first_name' => $validated['first_name'],
                    'last_name'  => strtoupper($validated['last_name']),
                    'email'      => $validated['email'],
                    'password'   => $validated['password'], 
                    'role_id'    => $validated['role_id'],
                    'photo'      => $path,
                ]);
            });

            // On recharge le rôle pour être sûr d'avoir le nom frais
            $roleNom = strtolower($user->role->nom ?? '');

            if ($roleNom === 'etudiant') {
                return redirect()->route('admin.etudiants.index', ['new_user_id' => $user->id])
                    ->with('success', "Compte étudiant créé avec succès.");
            }

            if ($roleNom === 'enseignant') {
                return redirect()->route('admin.teachers.index', ['new_user_id' => $user->id])
                    ->with('success', "Compte enseignant créé avec succès.");
            }

            return redirect()->route('admin.users.index')
                ->with('success', "L'utilisateur {$user->first_name} a été créé.");

        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getTraceAsString()); // Arrête tout et affiche l'erreur
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
            'password'   => 'required|string|min:4|confirmed', // 'min:4' au lieu de 'min:8'
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $user) {
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
                    $user->password = $validated['password'];
                }

                $user->save();
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
            return back()->with('error', "Vous ne pouvez pas supprimer votre propre compte.");
        }

        try {
            DB::transaction(function () use ($user) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                // Suppression automatique des profils liés
                $user->studentProfile()->delete();
                $user->teacherProfile()->delete();
                
                $user->delete();
            });

            return redirect()->route('admin.users.index')
                ->with('success', "Utilisateur supprimé définitivement.");

        } catch (\Exception $e) {
            return back()->with('error', "Impossible de supprimer : cet utilisateur possède des données liées.");
        }
    }
}