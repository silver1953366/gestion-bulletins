<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 20px; }
        .left-header { float: left; width: 45%; text-align: center; }
        .right-header { float: right; width: 45%; text-align: center; }
        .clear { clear: both; }
        .title { text-align: center; font-weight: bold; font-size: 14px; margin: 20px 0; text-decoration: underline; }
        .info-box { border: 1px solid #000; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .ue-row { background-color: #e2e8f0; font-weight: bold; text-align: left !important; }
        .footer { margin-top: 30px; }
        .signature { float: right; width: 200px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left-header">
            <strong>INSTITUT NATIONAL DE LA POSTE, DES TIC</strong><br>
            <strong>(INPTIC)</strong><br>
            <small>DIRECTION DES ETUDES ET DE LA PEDAGOGIE</small>
        </div>
        <div class="right-header">
            <strong>RÉPUBLIQUE GABONAISE</strong><br>
            <small>Union - Travail - Justice</small>
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">BULLETIN DE NOTES DU SEMESTRE 5</div>
    <div style="text-align: center; margin-bottom: 10px;">Année universitaire : {{ $annee->libelle ?? '2015/2016' }}</div>

    <div class="info-box">
        <strong>Nom(s) et Prénom(s) :</strong> {{ $etudiant->full_name }}<br>
        <strong>Classe :</strong> LP ASUR
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>DÉSIGNATION DES UNITÉS D'ENSEIGNEMENT</th>
                <th>Crédits</th>
                <th>Coeff</th>
                <th>Notes</th>
                <th>Moy. Classe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($unites_enseignement as $ue)
                <tr class="ue-row">
                    <td colspan="5">{{ $ue->libelle }}</td>
                </tr>
                @foreach($ue->matieres as $matiere)
                    <tr>
                        <td style="text-align: left;">{{ $matiere->libelle }}</td>
                        <td>{{ $matiere->credits }}</td>
                        <td>{{ $matiere->coefficient }}</td>
                        <td>{{ number_format($matiere->note_finale, 2) }}</td>
                        <td>{{ number_format($matiere->moyenne_classe, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="font-style: italic; background-color: #f8fafc;">
                    <td style="text-align: right;"><strong>Moyenne {{ $ue->code }}</strong></td>
                    <td>{{ $ue->total_credits }}</td>
                    <td>{{ $ue->total_coeffs }}</td>
                    <td colspan="2"><strong>{{ number_format($ue->moyenne, 2) }} / 20</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="font-weight: bold;">
            MOYENNE GÉNÉRALE DU SEMESTRE : {{ number_format($moyenne_semestre, 2) }} / 20<br>
            RÉSULTAT : {{ $decision }}
        </div>
        <div class="signature">
            Le Directeur des Études,<br><br><br>
            <strong>Signature & Cachet</strong>
        </div>
    </div>
</body>
</html>