<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use App\Models\AuditLog; // Assurez-vous que ce modèle existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParametreController extends Controller
{
    /**
     * Affiche la liste de tous les paramètres système.
     */
    public function index()
    {
        $parametres = Parametre::orderBy('cle')->get();
        return view('admin.parametres.index', compact('parametres'));
    }

    /**
     * Enregistre ou met à jour un paramètre dynamiquement.
     * Cette méthode gère à la fois la création et la modification (UpdateOrCreate).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cle'         => 'required|string|max:100',
            'valeur'      => 'required',
            'description' => 'nullable|string'
        ]);

        // Détection automatique du type de valeur (Nombre vs Texte)
        // Utile pour les quotas d'absences ou les seuils de moyenne
        $valeurOriginale = $validated['valeur'];
        $valeurFormattee = is_numeric($valeurOriginale) ? (float)$valeurOriginale : $valeurOriginale;

        // Utilisation de la méthode statique du modèle pour gérer le cache
        $param = Parametre::setValue(
            $validated['cle'], 
            $valeurFormattee, 
            $validated['description']
        );

        // Journalisation de l'action pour la sécurité (Audit Trail)
        $this->logAction($param);

        return redirect()->route('admin.parametres.index')
            ->with('success', "Le paramètre [{$param->cle}] a été mis à jour avec succès.");
    }

    /**
     * Supprime un paramètre de la configuration.
     */
    public function destroy(Parametre $parametre)
    {
        $cle = $parametre->cle;
        $parametre->delete();

        // Nettoyage manuel du cache au cas où
        \Illuminate\Support\Facades\Cache::forget("param_{$cle}");

        return back()->with('success', "Le paramètre [{$cle}] a été supprimé.");
    }

    /**
     * Méthode privée pour enregistrer l'activité dans l'audit log.
     */
    private function logAction(Parametre $param)
    {
        if (class_exists('App\Models\AuditLog')) {
            AuditLog::create([
                'action' => 'UPDATE_CONFIG',
                'model_type' => 'Parametre',
                'model_id' => $param->id,
                'user_id' => Auth::id(),
                'details' => "Modification de la clé : {$param->cle}",
                'new_values' => json_encode($param->valeur),
                'ip_address' => request()->ip(),
            ]);
        }
    }
}