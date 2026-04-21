<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        $departements = Departement::orderBy('nom')->paginate(10);
        return view('admin.departements.index', compact('departements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:departements,nom',
        ]);

        Departement::create($validated);

        return redirect()->route('admin.departements.index')
            ->with('success', 'Département ajouté avec succès.');
    }

    public function update(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:departements,nom,' . $departement->id,
        ]);

        $departement->update($validated);

        return redirect()->route('admin.departements.index')
            ->with('success', 'Département mis à jour.');
    }

    public function destroy(Departement $departement)
    {
        // Vérifier si le département contient des filières avant de supprimer
        if ($departement->filieres()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer : ce département contient encore des filières.');
        }

        $departement->delete();
        return back()->with('success', 'Département supprimé.');
    }
}