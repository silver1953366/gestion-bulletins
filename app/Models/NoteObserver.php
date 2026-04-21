<?php

namespace App\Observers;

use App\Models\Evaluation;
use App\Models\AuditLog;

class EvaluationObserver
{
    public function created(Evaluation $evaluation)
    {
        AuditLog::log('CREATE', 'Evaluation', $evaluation->id, null, $evaluation->toArray());
    }

    public function updating(Evaluation $evaluation)
    {
        // On récupère les valeurs avant modification
        $oldValues = $evaluation->getOriginal();
        // On récupère uniquement ce qui a changé
        $newValues = $evaluation->getDirty();

        AuditLog::log('UPDATE', 'Evaluation', $evaluation->id, $oldValues, $newValues);
    }

    public function deleted(Evaluation $evaluation)
    {
        AuditLog::log('DELETE', 'Evaluation', $evaluation->id, $evaluation->toArray(), null);
    }
}