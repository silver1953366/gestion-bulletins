<?php
// app/Http/Controllers/Secretariat/ResultatController.php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\ResultatAnnuel;

class ResultatController extends Controller
{
    public function index()
    {
        $resultats = ResultatAnnuel::with(['etudiant', 'anneeAcademique'])->paginate(20);
        return view('secretariat.resultats.index', compact('resultats'));
    }

    public function moyennes()
    {
        $etudiants = Etudiant::with(['resultatsMatieres', 'resultatsSemestres'])->get();
        return view('secretariat.resultats.moyennes', compact('etudiants'));
    }

    public function credits()
    {
        $etudiants = Etudiant::with(['resultatsSemestres'])->get();
        return view('secretariat.resultats.credits', compact('etudiants'));
    }

    public function jury()
    {
        $resultats = ResultatAnnuel::with(['etudiant', 'anneeAcademique'])
            ->whereNotNull('decision')
            ->get();
        
        $stats = [
            'admis' => $resultats->where('decision', 'ADMIS')->count(),
            'redoublants' => $resultats->where('decision', 'REDOUBLEMENT')->count(),
            'exclus' => $resultats->where('decision', 'EXCLU')->count(),
        ];
        
        return view('secretariat.resultats.jury', compact('resultats', 'stats'));
    }

    public function validateJury(Request $request)
    {
        $request->validate([
            'resultats' => 'required|array',
            'resultats.*.id' => 'exists:resultats_annuels,id',
            'resultats.*.decision' => 'required|in:ADMIS,REDOUBLEMENT,EXCLU',
        ]);
        
        foreach ($request->resultats as $data) {
            ResultatAnnuel::where('id', $data['id'])->update(['decision' => $data['decision']]);
        }
        
        return redirect()->route('secretariat.resultats.jury')
            ->with('success', 'Décisions du jury enregistrées');
    }
}