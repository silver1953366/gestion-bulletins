<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    /**
     * Enregistre l'affectation d'un étudiant à une classe pour une année donnée.
     */
    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'classe_id' => 'required|exists:classes,id',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
            'statut' => 'required|in:inscrit,redoublant,transfere'
        ]);

        // Empêche les doubles inscriptions pour la même année
        Inscription::updateOrCreate(
            [
                'etudiant_id' => $request->etudiant_id,
                'annee_academique_id' => $request->annee_academique_id
            ],
            [
                'classe_id' => $request->classe_id,
                'statut' => $request->statut
            ]
        );

        return back()->with('success', 'L\'étudiant a été inscrit avec succès.');
    }
}