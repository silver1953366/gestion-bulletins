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

                // Le mot de passe est hashé automatiquement via le cast 'hashed' dans le modèle User
                return User::create([
                    'first_name' => $validated['first_name'],
                    'last_name'  => strtoupper($validated['last_name']),
                    'email'      => $validated['email'],
                    'password'   => $validated['password'], 
                    'role_id'    => $validated['role_id'],
                    'photo'      => $path,
                ]);
            });

            // Logique de redirection selon le rôle (étudiant, enseignant, etc.)
            $roleNom = strtolower($user->role->nom ?? '');

            if ($roleNom === 'etudiant') {
                return redirect()->route('admin.etudiants.index', ['new_user_id' => $user->id])
                    ->with('success', "Compte étudiant créé. Veuillez compléter son profil académique.");
            }

            if ($roleNom === 'enseignant') {
                return redirect()->route('admin.teachers.index', ['new_user_id' => $user->id])
                    ->with('success', "Compte enseignant créé. Veuillez compléter son profil professionnel.");
            }

            return redirect()->route('admin.users.index')
                ->with('success', "L'utilisateur {$user->first_name} a été créé avec succès.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', "Erreur lors de la création : " . $e->getMessage());
        }
    }

    /**
     * Mise à jour de l'utilisateur.
     */
    public function update(Request $request, User $user)
    {
        // On rend le mot de passe 'nullable' pour permettre la modification sans changer le pass
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
                // Gestion de la nouvelle photo
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

                // Mise à jour du mot de passe uniquement s'il est renseigné
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
        // Sécurité : Ne pas se supprimer soi-même
        if (auth()->id() === $user->id) {
            return back()->with('error', "Action impossible : vous ne pouvez pas supprimer votre propre compte.");
        }

        try {
            DB::transaction(function () use ($user) {
                // Supprimer la photo physiquement du stockage
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }

                // Suppression en cascade des profils liés pour éviter les erreurs SQL (Integrity Constraint)
                if ($user->studentProfile) {
                    $user->studentProfile()->delete();
                }
                if ($user->teacherProfile) {
                    $user->teacherProfile()->delete();
                }
                
                $user->delete();
            });

            return redirect()->route('admin.users.index')
                ->with('success', "Utilisateur et données associées supprimés.");

        } catch (\Exception $e) {
            return back()->with('error', "Erreur de suppression : cet utilisateur possède peut-être des données liées (notes, absences) protégées.");
        }
    }
}