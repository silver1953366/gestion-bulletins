{{-- resources/views/secretariat/bulletins/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de notes - {{ $etudiant->prenom }} {{ $etudiant->nom }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0d9488;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-etudiant {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .info-etudiant table {
            width: 100%;
        }
        .info-etudiant td {
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0d9488;
            color: white;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .mention {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background: #f0fdf4;
            border-radius: 5px;
        }
        .decision {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .decision.admis {
            background: #dcfce7;
            color: #166534;
        }
        .decision.non-admis {
            background: #fee2e2;
            color: #991b1b;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INSTITUT NATIONAL DE LA PROPRIÉTÉ INDUSTRIELLE</h1>
        <p>Direction des Enseignements</p>
        <h2>BULLETIN DE NOTES</h2>
        <p>Année académique: {{ $annee_academique->libelle ?? '2024-2025' }}</p>
        <p>Type: Bulletin {{ $type }}</p>
    </div>

    <div class="info-etudiant">
        <table>
            <tr>
                <td width="50%"><strong>Nom:</strong> {{ $etudiant->nom }}</td>
                <td width="50%"><strong>Prénom(s):</strong> {{ $etudiant->prenom }}</td>
            </tr>
            <tr>
                <td><strong>Date de naissance:</strong> {{ $etudiant->date_naissance ?? 'Non renseignée' }}</td>
                <td><strong>Lieu de naissance:</strong> {{ $etudiant->lieu_naissance ?? 'Non renseigné' }}</td>
            </tr>
            <tr>
                <td><strong>Baccalauréat:</strong> {{ $etudiant->bac ?? 'Non renseigné' }}</td>
                <td><strong>Provenance:</strong> {{ $etudiant->provenance ?? 'Non renseignée' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Unité d'Enseignement / Matière</th>
                <th class="text-center">Coefficient</th>
                <th class="text-center">Crédits</th>
                <th class="text-center">Note /20</th>
                <th class="text-center">Moyenne UE</th>
                <th class="text-center">Crédits acquis</th>
            </tr>
        </thead>
        <tbody>
            @php
                $groupedMatieres = $matieres->groupBy(function($item) {
                    return $item->matiere->ue->libelle ?? 'Sans UE';
                });
            @endphp
            @foreach($groupedMatieres as $ueLibelle => $matieresUE)
                <tr style="background-color: #e2e8f0;">
                    <td colspan="6" class="font-bold">{{ $ueLibelle }}</td>
                </tr>
                @foreach($matieresUE as $resultat)
                    @php
                        $moyenne = $resultat->moyenne ?? 0;
                        $valide = $moyenne >= 10;
                        $credits = $resultat->matiere->credits ?? 0;
                        $creditsAcquis = $valide ? $credits : 0;
                    @endphp
                    <tr>
                        <td style="padding-left: 20px;">• {{ $resultat->matiere->libelle ?? 'N/A' }}</td>
                        <td class="text-center">{{ $resultat->matiere->coefficient ?? 1 }}</td>
                        <td class="text-center">{{ $credits }}</td>
                        <td class="text-center">
                            <strong>{{ number_format($moyenne, 2) }}</strong>
                        </td>
                        <td class="text-center">-</td>
                        <td class="text-center">{{ $creditsAcquis }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f0fdf4;">
                <td colspan="4" class="font-bold text-center">TOTAUX</td>
                <td class="text-center font-bold">{{ number_format($moyenne_generale, 2) }}</td>
                <td class="text-center font-bold">{{ $credits_obtenus ?? 0 }}</td>
             </tr>
        </tfoot>
    </table>

    <div class="mention">
        Mention: <strong>{{ $mention ?? 'Non déterminée' }}</strong>
    </div>

    <div class="decision {{ strtolower($decision) == 'admis' || strtolower($decision) == 'validé' ? 'admis' : 'non-admis' }}">
        Décision: <strong>{{ $decision ?? 'En attente' }}</strong>
    </div>

    <div class="footer">
        <p>Fait à Libreville, le {{ date('d/m/Y') }}</p>
        <p>Le Secrétariat Pédagogique</p>
    </div>
</body>
</html>