<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    public function index()
    {
        $etudiants = Etudiant::latest()->paginate(10);
        return view('admin.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
        return view('admin.etudiants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'nullable|email',
        ]);

        Etudiant::create($data);

        return redirect()->route('admin.etudiants.index')->with('success', 'Étudiant ajouté');
    }

    public function show(Etudiant $etudiant)
    {
        return view('admin.etudiants.show', compact('etudiant'));
    }

    public function edit(Etudiant $etudiant)
    {
        return view('admin.etudiants.edit', compact('etudiant'));
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $data = $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'nullable|email',
        ]);

        $etudiant->update($data);

        return redirect()->route('admin.etudiants.index')->with('success', 'Mis à jour');
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();

        return back()->with('success', 'Supprimé');
    }
}