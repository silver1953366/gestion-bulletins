<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    /**
     * Liste tous les paramètres système.
     */
    public function index()
    {
        $parametres = Parametre::orderBy('cle')->get();
        return view('admin.parametres.index', compact('parametres'));
    }

    /**
     * Enregistre ou met à jour un paramètre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cle'         => 'required|string|max:100',
            'valeur'      => 'required',
            'description' => 'nullable|string'
        ]);

        // On utilise la méthode statique de votre modèle
        $param = Parametre::setValue(
            $validated['cle'], 
            $validated['valeur'], 
            $validated['description']
        );

        // On log l'action dans l'AuditLog
        AuditLog::log('UPDATE_CONFIG', 'Parametre', $param->id, null, $param->valeur);

        return redirect()->route('admin.parametres.index')
            ->with('success', 'Configuration mise à jour avec succès.');
    }

    /**
     * Supprime un paramètre (à utiliser avec prudence).
     */
    public function destroy(Parametre $parametre)
    {
        $parametre->delete();
        return back()->with('success', 'Paramètre supprimé.');
    }
}