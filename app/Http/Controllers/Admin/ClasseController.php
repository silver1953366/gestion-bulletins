<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes avec leurs relations.
     */
    public function index()
    {
        $classes = Classe::with(['filiere', 'niveau'])->orderBy('nom')->paginate(10);
        $filieres = Filiere::all();
        $niveaux = Niveau::all();
        
        // Récupération de l'année active pour pré-remplir le formulaire
        $anneeActive = AnneeAcademique::where('active', true)->first();

        return view('admin.classes.index', compact('classes', 'filieres', 'niveaux', 'anneeActive'));
    }

    /**
     * Enregistre une nouvelle classe.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'                 => 'required|string|max:100',
            'filiere_id'          => 'required|exists:filieres,id',
            'niveau_id'           => 'required|exists:niveaux,id',
            'annee_universitaire' => 'required|string|max:20',
        ]);

        Classe::create($validated);

        return redirect()->route('admin.classes.index')
            ->with('success', 'La classe a été créée avec succès.');
    }

    /**
     * Met à jour une classe existante.
     * Note : On utilise l'ID ($id) pour garantir la récupération en base de données.
     */
    public function update(Request $request, $id)
    {
        try {
            $classe = Classe::findOrFail($id);

            $validated = $request->validate([
                'nom'                 => 'required|string|max:100',
                'filiere_id'          => 'required|exists:filieres,id',
                'niveau_id'           => 'required|exists:niveaux,id',
                'annee_universitaire' => 'required|string|max:20',
            ]);

            // On vérifie si des modifications ont réellement été faites
            $classe->fill($validated);
            
            if ($classe->isDirty()) {
                $classe->save();
                return redirect()->route('admin.classes.index')
                    ->with('success', 'Classe mise à jour avec succès.');
            }

            return redirect()->route('admin.classes.index')
                ->with('info', 'Aucune modification apportée.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Erreur update classe ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la modification.');
        }
    }

    /**
     * Supprime une classe.
     */
    public function destroy($id)
    {
        try {
            $classe = Classe::findOrFail($id);
            $classe->delete();

            return redirect()->route('admin.classes.index')
                ->with('success', 'La classe a été supprimée définitivement.');
        } catch (\Exception $e) {
            Log::error("Erreur suppression classe ID {$id}: " . $e->getMessage());
            return back()->with('error', 'Impossible de supprimer cette classe.');
        }
    }
}