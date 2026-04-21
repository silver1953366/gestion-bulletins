<!DOCTYPE html>
<html>
<head>
    <title>Liste des étudiants</title>
</head>
<body>

<h2>Liste des étudiants</h2>

<a href="{{ route('admin.etudiants.create') }}">Ajouter un étudiant</a>

<table border="1" cellpadding="10">
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Actions</th>
    </tr>

    @foreach($etudiants as $etudiant)
    <tr>
        <td>{{ $etudiant->nom }}</td>
        <td>{{ $etudiant->prenom }}</td>

        <td>
            <a href="{{ route('admin.etudiants.show', $etudiant->id) }}">
                👤 Gérer profil
            </a>
        </td>
    </tr>
    @endforeach

</table>

</body>
</html>