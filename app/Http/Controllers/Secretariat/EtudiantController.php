<?php
// app/Http/Controllers/Secretariat/EtudiantController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Support\Facades\DB;

class EtudiantController extends Controller
{
    public function index()
    {
        $etudiants = Etudiant::with(['inscriptions.classe'])->paginate(20);
        return view('secretariat.etudiants.index', compact('etudiants'));
    }

    public function create()
    {
        $classes = Classe::all();
        return view('secretariat.etudiants.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'bac' => 'nullable|string|max:50',
            'provenance' => 'nullable|string|max:100',
            'classe_id' => 'nullable|exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            // Créer l'étudiant
            $etudiant = Etudiant::create($request->except('classe_id'));
            
            // Inscrire dans une classe si spécifiée
            if ($request->classe_id) {
                $anneeActive = AnneeAcademique::where('active', true)->first();
                Inscription::create([
                    'etudiant_id' => $etudiant->id,
                    'classe_id' => $request->classe_id,
                    'annee_academique_id' => $anneeActive ? $anneeActive->id : null,
                    'statut' => 'inscrit'
                ]);
            }
            
            DB::commit();
            return redirect()->route('secretariat.etudiants.index')
                ->with('success', 'Étudiant ajouté avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'ajout');
        }
    }

    public function show($id)
    {
        $etudiant = Etudiant::with(['inscriptions.classe', 'inscriptions.anneeAcademique'])->findOrFail($id);
        return view('secretariat.etudiants.show', compact('etudiant'));
    }

    public function edit($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $classes = Classe::all();
        return view('secretariat.etudiants.edit', compact('etudiant', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
            'bac' => 'nullable|string|max:50',
            'provenance' => 'nullable|string|max:100',
        ]);

        $etudiant = Etudiant::findOrFail($id);
        $etudiant->update($request->all());
        
        return redirect()->route('secretariat.etudiants.index')
            ->with('success', 'Étudiant modifié avec succès');
    }

    public function destroy($id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $etudiant->delete();
        
        return redirect()->route('secretariat.etudiants.index')
            ->with('success', 'Étudiant supprimé');
    }

    public function fiche($id)
    {
        $etudiant = Etudiant::with([
            'inscriptions.classe.filiere',
            'evaluations.matiere',
            'absences.matiere',
            'resultatsMatieres.matiere',
            'resultatsSemestres.semestre',
            'resultatsAnnuel'
        ])->findOrFail($id);
        
        return view('secretariat.etudiants.fiche', compact('etudiant'));
    }
}