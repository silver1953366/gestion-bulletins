<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Etudiant;
use App\Models\StudentProfile;
use App\Models\Inscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class EtudiantController extends Controller
{ 

public function store(Request $request)
{
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
}