<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ImportNote;
use App\Imports\NotesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImportNoteController extends Controller
{
    public function index()
    {
        $imports = ImportNote::with('createdBy')->latest()->paginate(10);
        return view('admin.imports.index', compact('imports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fichier_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $file = $request->file('fichier_excel');
        $path = $file->store('imports', 'public');

        // Création de l'enregistrement d'import
        $importRecord = ImportNote::create([
            'fichier' => $path,
            'statut' => 'pending',
            'created_by' => Auth::id(),
        ]);

        try {
            // Exécution de l'import
            Excel::import(new NotesImport, storage_path('app/public/' . $path));
            
            // Mise à jour en cas de succès
            $importRecord->update(['statut' => 'success']);
            
            return back()->with('success', 'Importation des notes réussie !');
            
        } catch (\Exception $e) {
            // Mise à jour en cas d'échec
            $importRecord->update(['statut' => 'failed']);
            
            return back()->with('error', 'Erreur lors de l\'importation : ' . $e->getMessage());
        }
    }

    public function destroy(ImportNote $importNote)
    {
        Storage::disk('public')->delete($importNote->fichier);
        $importNote->delete();
        return back()->with('success', 'Historique supprimé.');
    }
}