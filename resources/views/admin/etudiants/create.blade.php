<!DOCTYPE html>
<html>
<head>
    <title>Ajouter étudiant</title>
</head>
<body>

<h2>Ajouter un étudiant</h2>

<form action="{{ route('admin.etudiants.store') }}" method="POST">
    @csrf

    <input type="text" name="nom" placeholder="Nom"><br>
    <input type="text" name="prenom" placeholder="Prénom"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Mot de passe"><br>

    <button type="submit">Enregistrer</button>
</form>

</body>
</html>