<<<<<<< HEAD
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
=======
@extends('admin.layouts.master')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in pb-12">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <a href="{{ route('admin.etudiants.index') }}" class="group inline-flex items-center gap-2 text-indigo-600 font-black text-xs uppercase tracking-widest mb-3 hover:text-indigo-800 transition">
                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Retour à la liste
            </a>
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-[1.5rem] bg-slate-900 flex items-center justify-center text-white text-2xl font-black italic shadow-2xl uppercase">
                    {{ substr($etudiant->nom, 0, 1) }}{{ substr($etudiant->prenom, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">{{ $etudiant->full_name }}</h1>
                    <p class="text-indigo-600 font-bold italic mt-1">Étudiant LP ASUR — INPTIC</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.etudiants.edit', $etudiant) }}" class="px-6 py-3 bg-white border-2 border-slate-100 rounded-2xl font-black text-xs uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition shadow-sm">
                Modifier la fiche
            </a>
            <button onclick="window.print()" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                Imprimer Bilan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">État Civil & Origine</h3>
                
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Matricule</p>
                        <p class="text-lg font-black text-slate-900 italic">{{ $etudiant->matricule ?? 'NON GÉNÉRÉ' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Date et Lieu de Naissance</p>
                        <p class="font-bold text-slate-700">
                            {{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d F Y') : 'N/C' }}
                        </p>
                        <p class="text-sm font-medium text-slate-500 italic">à {{ $etudiant->lieu_naissance ?? 'N/C' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cursus d'origine</p>
                        <p class="font-bold text-slate-700">{{ $etudiant->bac ?? 'N/C' }}</p>
                        <p class="text-sm font-medium text-slate-500 italic">{{ $etudiant->provenance ?? 'N/C' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-rose-50 rounded-[2.5rem] p-8 border border-rose-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em]">Assiduité</h3>
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                </div>
                <p class="text-4xl font-black text-rose-600 italic tracking-tighter">
                    {{ $etudiant->absences->sum('heures') }} <span class="text-sm uppercase tracking-normal">Heures</span>
                </p>
                <p class="text-xs font-bold text-rose-400 mt-1 italic">Total des absences cumulées</p>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach(['S5', 'S6'] as $semestre)
                @php 
                    $resultat = $etudiant->resultatsSemestres->where('semestre_id', ($semestre == 'S5' ? 1 : 2))->first();
                @endphp
                <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10 font-black text-6xl italic">{{ $semestre }}</div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Résultats {{ $semestre }}</h3>
                    
                    <div class="flex items-end gap-2 mb-2">
                        <p class="text-4xl font-black text-slate-900 tracking-tighter italic">
                            {{ number_format($resultat->moyenne_semestre ?? 0, 2) }}
                        </p>
                        <p class="text-slate-400 font-bold mb-1 italic">/ 20</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: {{ (($resultat->crédits_total ?? 0) / 30) * 100 }}%"></div>
                        </div>
                        <p class="text-xs font-black text-indigo-600 uppercase tracking-widest">
                            {{ $resultat->crédits_total ?? 0 }} / 30 ECTS
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Dernières Évaluations</h3>
                    <a href="{{ route('admin.notes.index', ['etudiant' => $etudiant->id]) }}" class="text-xs font-black text-indigo-600 uppercase tracking-widest hover:underline">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4">Matière</th>
                                <th class="px-8 py-4">Type</th>
                                <th class="px-8 py-4 text-right">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($etudiant->evaluations()->latest()->take(5)->get() as $eval)
                            <tr class="text-sm font-bold text-slate-700">
                                <td class="px-8 py-4 italic">{{ $eval->matiere->libellé }}</td>
                                <td class="px-8 py-4">
                                    <span class="px-2 py-1 bg-slate-100 rounded text-[9px] font-black uppercase tracking-widest">
                                        {{ $eval->type }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right font-black text-indigo-600">{{ number_format($eval->note, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-10 text-center text-slate-400 italic text-sm">Aucune note enregistrée pour le moment.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @php $annuel = $etudiant->resultatsAnnuel->first(); @endphp
            @if($annuel)
            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Décision du Jury Académique</p>
                        <h2 class="text-3xl font-black italic tracking-tighter uppercase text-indigo-400">
                            {{ $annuel->décision_jury }}
                        </h2>
                        <p class="text-slate-400 text-sm mt-2 font-medium italic">
                            Mention : <span class="text-white">{{ $annuel->mention }}</span> — Moyenne Annuelle : <span class="text-white">{{ number_format($annuel->moyenne_annuelle, 2) }}</span>
                        </p>
                    </div>
                    <div class="w-px h-12 bg-slate-800 hidden md:block"></div>
                    <div class="text-center">
                        <p class="text-5xl font-black italic tracking-tighter text-indigo-500">60</p>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Crédits Validés</p>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    @media print {
        aside, nav, button, a { display: none !important; }
        .bg-slate-50 { background-color: white !important; }
        .shadow-sm, .shadow-2xl { shadow: none !important; border: 1px solid #eee !important; }
        .max-w-6xl { max-width: 100% !important; margin: 0 !important; }
    }
</style>
@endsection
>>>>>>> 6f3d284 (Initialisation ERP INPTIC : Sidebar et Layout fonctionnels)
