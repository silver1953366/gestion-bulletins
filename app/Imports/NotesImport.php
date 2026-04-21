<?php

namespace App\Imports;

use App\Models\Note;
use App\Models\Etudiant;
use App\Models\Matiere;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class NotesImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Utilisation de ToCollection pour un contrôle plus fin 
     * (vérification de l'existence des IDs avant insertion)
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Recherche de l'étudiant par matricule (Identifiant unique INPTIC)
            $etudiant = Etudiant::where('matricule', $row['matricule'])->first();
            
            // 2. Recherche de la matière par son code (ex: MAT101)
            $matiere = Matiere::where('code', $row['code_matiere'])->first();

            if ($etudiant && $matiere) {
                // 3. Mise à jour ou Création de la note (évite les doublons)
                Note::updateOrCreate(
                    [
                        'etudiant_id' => $etudiant->id,
                        'matiere_id'  => $matiere->id,
                        'type'        => strtoupper($row['type']), // CC, EXAMEN, RATTRAPAGE
                    ],
                    [
                        'valeur' => $row['note'],
                    ]
                );
            }
        }
    }

    /**
     * Règles de validation pour chaque ligne du fichier Excel
     */
    public function rules(): array
    {
        return [
            'matricule'    => 'required',
            'code_matiere' => 'required',
            'note'         => 'required|numeric|min:0|max:20',
            'type'         => 'required|in:CC,EXAMEN,RATTRAPAGE,cc,examen,rattrapage',
        ];
    }

    /**
     * Personnalisation des messages d'erreur
     */
    public function customValidationMessages()
    {
        return [
            'note.max' => 'La note ne peut pas dépasser 20/20.',
            'type.in'  => 'Le type de note doit être : CC, EXAMEN ou RATTRAPAGE.',
        ];
    }
}