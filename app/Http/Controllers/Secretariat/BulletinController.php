<?php
// app/Http/Controllers/Secretariat/AbsenceController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Inscription;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with(['etudiant', 'matiere']);
        
        if ($request->etudiant) {
            $query->where('etudiant_id', $request->etudiant);
        }
        
        if ($request->matiere) {
            $query->where('matiere_id', $request->matiere);
        }
        
        $absences = $query->orderBy('created_at', 'desc')->paginate(20);
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        
        return view('secretariat.absences.index', compact('absences', 'etudiants', 'matieres'));
    }

    public function create()
    {
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        return view('secretariat.absences.create', compact('etudiants', 'matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'heures' => 'required|integer|min:1|max:100',
            'justification' => 'nullable|string',
        ]);
        
        Absence::create([
            'etudiant_id' => $request->etudiant_id,
            'matiere_id' => $request->matiere_id,
            'heures' => $request->heures,
            'justification' => $request->justification,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('secretariat.absences.index')
            ->with('success', 'Absence enregistrée');
    }

    public function edit($id)
    {
        $absence = Absence::findOrFail($id);
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        return view('secretariat.absences.edit', compact('absence', 'etudiants', 'matieres'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'matiere_id' => 'required|exists:matieres,id',
            'heures' => 'required|integer|min:1|max:100',
            'justification' => 'nullable|string',
        ]);
        
        $absence = Absence::findOrFail($id);
        $absence->update($request->all());
        
        return redirect()->route('secretariat.absences.index')
            ->with('success', 'Absence modifiée');
    }

    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $absence->delete();
        
        return redirect()->route('secretariat.absences.index')
            ->with('success', 'Absence supprimée');
    }

    public function penalites($etudiantId)
    {
        $etudiant = Etudiant::findOrFail($etudiantId);
        $totalAbsences = Absence::where('etudiant_id', $etudiantId)->sum('heures');
        
        // Calcul des pénalités (exemple: 1 point de pénalité par 10 heures d'absence)
        $penalites = floor($totalAbsences / 10);
        
        $absences = Absence::where('etudiant_id', $etudiantId)
            ->with('matiere')
            ->get();
        
        return view('secretariat.absences.penalites', compact('etudiant', 'totalAbsences', 'penalites', 'absences'));
    }
}