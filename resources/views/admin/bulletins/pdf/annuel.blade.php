<div class="title">BULLETIN DE NOTES ANNUEL</div>

<table class="table">
    <thead>
        <tr style="background-color: #cbd5e1;">
            <th>UNITÉS D'ENSEIGNEMENT</th>
            <th>COEFF</th>
            <th>MOYENNE S5</th>
            <th>MOYENNE S6</th>
            <th>MOYENNE ANNUELLE</th>
            <th>RÉSULTAT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resultats_annuels as $res)
        <tr>
            <td style="text-align: left;">{{ $res['ue_libelle'] }}</td>
            <td>{{ $res['coeff'] }}</td>
            <td>{{ number_format($res['moy_s5'], 2) }}</td>
            <td>{{ number_format($res['moy_s6'], 2) }}</td>
            <td><strong>{{ number_format($res['moy_annuelle'], 2) }}</strong></td>
            <td>{{ $res['statut'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="info-box" style="margin-top: 20px;">
    <strong>MOYENNE GÉNÉRALE ANNUELLE : {{ number_format($total_annee, 2) }} / 20</strong><br>
    <strong>TOTAL CRÉDITS ACQUIS : {{ $total_credits }} / 60</strong><br>
    <strong>DÉCISION DU JURY : {{ $decision_finale }}</strong>
</div>