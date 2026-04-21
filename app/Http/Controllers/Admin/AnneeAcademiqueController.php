<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class AnneeAcademiqueController extends Controller
{
    public function index()
    {
        $annees = AnneeAcademique::orderBy('libelle', 'desc')->paginate(10);
        return view('admin.annees.index', compact('annees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:20|unique:annees_academiques,libelle',
            'active'  => 'boolean'
        ]);

        // Si on demande d'activer cette nouvelle année, on désactive les autres
        if ($request->has('active') && $request->active) {
            AnneeAcademique::where('active', true)->update(['active' => false]);
        }

        AnneeAcademique::create($validated);

        return redirect()->route('admin.annees.index')->with('success', 'Année académique créée.');
    }

    public function update(Request $request, AnneeAcademique $annee)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:20|unique:annees_academiques,libelle,' . $annee->id,
            'active'  => 'boolean'
        ]);

        // Logique de basculement de l'année active
        if ($request->has('active') && $request->active) {
            AnneeAcademique::where('id', '!=', $annee->id)->update(['active' => false]);
        }

        $annee->update($validated);

        return redirect()->route('admin.annees.index')->with('success', 'Mise à jour effectuée.');
    }

    public function destroy(AnneeAcademique $annee)
    {
        if ($annee->active) {
            return back()->with('error', 'Impossible de supprimer l\'année active.');
        }
        $annee->delete();
        return back()->with('success', 'Année supprimée.');
    }
}