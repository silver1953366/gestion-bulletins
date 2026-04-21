<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Affiche la liste chronologique des actions système.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filtre optionnel par action (CREATE, UPDATE, DELETE)
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        $logs = $query->paginate(20);

        return view('admin.audit.index', compact('logs'));
    }

    /**
     * Affiche les détails d'une modification (Anciennes vs Nouvelles valeurs).
     */
    public function show(AuditLog $auditLog)
    {
        return view('admin.audit.show', compact('auditLog'));
    }

    /**
     * Optionnel : Nettoyage des vieux logs (plus de 6 mois par exemple)
     */
    public function clearOldLogs()
    {
        // AuditLog::where('created_at', '<', now()->subMonths(6))->delete();
        return back()->with('success', 'Journal nettoyé.');
    }
}