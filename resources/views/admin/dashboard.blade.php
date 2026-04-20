<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 15px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin: 0;
            font-size: 14px;
            color: #888;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            padding: 6px 10px;
            margin-top: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }

        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }

        .progress {
            height: 10px;
            background: #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            height: 10px;
            background: green;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .warning { background: #fff3cd; }
        .danger { background: #f8d7da; }
        .info { background: #d1ecf1; }
    </style>
</head>

<body>

<h1>📊 Dashboard Administrateur</h1>

<!-- ===================== -->
<!-- GLOBAL -->
<!-- ===================== -->
<h2>Statistiques globales</h2>

<div class="grid">

    <div class="card">
        <h3>Étudiants</h3>
        <p>{{ $stats['total_etudiants'] }}</p>
        <a href="{{ route('admin.etudiants.index') }}" class="btn btn-primary">Voir</a>
        <a href="{{ route('admin.etudiants.create') }}" class="btn btn-success">Ajouter</a>
    </div>

    <div class="card">
        <h3>Enseignants</h3>
        <p>{{ $stats['total_enseignants'] }}</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Gérer</a>
    </div>

    <div class="card">
        <h3>Matières</h3>
        <p>{{ $stats['total_matieres'] }}</p>
        <a href="{{ route('admin.matieres.index') }}" class="btn btn-primary">Voir</a>
        <a href="{{ route('admin.matieres.create') }}" class="btn btn-success">Ajouter</a>
    </div>

    <div class="card">
        <h3>UE</h3>
        <p>{{ $stats['total_ues'] }}</p>
        <a href="{{ route('admin.ues.index') }}" class="btn btn-primary">Voir</a>
    </div>

    <div class="card">
        <h3>Absences</h3>
        <p>{{ $stats['total_absences'] }}</p>
        <a href="{{ route('admin.absences.index') }}" class="btn btn-warning">Gérer</a>
    </div>

    <div class="card">
        <h3>Bulletins</h3>
        <p>{{ $stats['total_bulletins'] }}</p>
        <a href="{{ route('admin.bulletins.index') }}" class="btn btn-primary">Voir</a>
    </div>

</div>

<br>

<h3>Taux de remplissage des notes</h3>
<div class="progress">
    <div class="progress-bar" style="width: {{ $stats['taux_remplissage_notes'] }}%"></div>
</div>
<p>{{ $stats['taux_remplissage_notes'] }} %</p>

<!-- ===================== -->
<!-- ACADEMIQUE -->
<!-- ===================== -->
<h2>Performances académiques</h2>

<div class="grid">

    <div class="card">
        <h3>Moyenne générale</h3>
        <p>{{ $academicStats['moyenne_generale'] }}</p>
    </div>

    <div class="card">
        <h3>Taux réussite</h3>
        <p>{{ $academicStats['taux_reussite'] }}%</p>
    </div>

    <div class="card">
        <h3>S5</h3>
        <p>{{ $academicStats['moyenne_s5'] }}</p>
    </div>

    <div class="card">
        <h3>S6</h3>
        <p>{{ $academicStats['moyenne_s6'] }}</p>
    </div>

</div>

<!-- ===================== -->
<!-- JURY -->
<!-- ===================== -->
<h2>Top étudiants</h2>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Moyenne</th>
            <th>Mention</th>
        </tr>
    </thead>

    <tbody>
        @foreach($juryDecisions['top_5'] as $e)
        <tr>
            <td>{{ $e['nom'] }}</td>
            <td>{{ $e['prenom'] }}</td>
            <td>{{ $e['moyenne'] }}</td>
            <td>{{ $e['mention'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- ===================== -->
<!-- ALERTES -->
<!-- ===================== -->
<h2>Alertes</h2>

@forelse($alerts as $alert)
    <div class="alert {{ $alert['type'] }}">
        {{ $alert['message'] }}
        <br>
        <a href="{{ $alert['action'] }}" class="btn btn-danger">Corriger</a>
    </div>
@empty
    <p>Aucune alerte</p>
@endforelse

<!-- ===================== -->
<!-- ACTIVITES -->
<!-- ===================== -->
<h2>Activités récentes</h2>

<table>
    <thead>
        <tr>
            <th>Utilisateur</th>
            <th>Action</th>
            <th>Modèle</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach($recentActivities as $act)
        <tr>
            <td>{{ $act['user'] }}</td>
            <td>{{ $act['action'] }}</td>
            <td>{{ $act['model'] }}</td>
            <td>{{ $act['created_at'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
