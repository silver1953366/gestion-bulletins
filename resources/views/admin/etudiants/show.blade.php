<!DOCTYPE html>
<html>
<head>
    <title>Profil étudiant</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .box { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; }
        h2 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; }
        .empty { color: red; font-style: italic; }
        .btn { padding: 8px 12px; background: #3490dc; color: white; text-decoration: none; }
        .btn-warning { background: #f39c12; }
        .btn-success { background: #38c172; }
    </style>
</head>
<body>

<a href="{{ route('admin.etudiants.index') }}">⬅ Retour</a>

<h1>👤 Profil étudiant</h1>

{{-- ========================= --}}
{{-- 1. INFOS ETUDIANT --}}
{{-- ========================= --}}
<div class="box">
    <h2>Informations personnelles</h2>

    <p><strong>Nom :</strong>
        {{ $etudiant->nom ?? '<span class="empty">Non renseigné</span>' }}
    </p>

    <p><strong>Prénom :</strong>
        {{ $etudiant->prenom ?? '<span class="empty">Non renseigné</span>' }}
    </p>

    <p><strong>Date naissance :</strong>
        {{ $etudiant->date_naissance ?? '<span class="empty">Non renseigné</span>' }}
    </p>

    <p><strong>Bac :</strong>
        {{ $etudiant->bac ?? '<span class="empty">Non renseigné</span>' }}
    </p>

    <p><strong>Provenance :</strong>
        {{ $etudiant->provenance ?? '<span class="empty">Non renseigné</span>' }}
    </p>

    <a class="btn btn-warning" href="#">
        ✏️ Modifier infos étudiant
    </a>
</div>

{{-- ========================= --}}
{{-- 2. PROFIL USER --}}
{{-- ========================= --}}
<div class="box">
    <h2>🔐 Profil utilisateur</h2>

    @if($etudiant->studentProfile)
        <p><strong>Matricule :</strong> {{ $etudiant->studentProfile->matricule }}</p>
    @else
        <p class="empty">Aucun profil utilisateur lié</p>

        <a class="btn btn-success" href="#">
            ➕ Créer profil utilisateur
        </a>
    @endif
</div>

{{-- ========================= --}}
{{-- 3. INSCRIPTIONS --}}
{{-- ========================= --}}
<div class="box">
    <h2>🏫 Classes / Inscriptions</h2>

    <a class="btn btn-success" href="#">
        ➕ Ajouter inscription
    </a>

    <br><br>

    @if($etudiant->inscriptions && count($etudiant->inscriptions) > 0)

        <table>
            <tr>
                <th>Classe</th>
                <th>Année académique</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>

            @foreach($etudiant->inscriptions as $inscription)
                <tr>
                    <td>{{ $inscription->classe->nom ?? '---' }}</td>
                    <td>{{ $inscription->anneeAcademique->libelle ?? '---' }}</td>
                    <td>{{ $inscription->statut }}</td>
                    <td>
                        <a href="#" class="btn btn-warning">Modifier</a>
                    </td>
                </tr>
            @endforeach

        </table>

    @else
        <p class="empty">Aucune inscription trouvée</p>
    @endif
</div>

</body>
</html>