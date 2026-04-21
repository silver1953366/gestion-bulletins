<<<<<<< HEAD
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
=======
@extends('admin.layouts.master')

@section('content')
<div class="animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Gestion des Étudiants</h1>
            <p class="text-slate-500 text-sm font-bold italic mt-1">Promotion LP ASUR — INPTIC 2025-2026</p>
        </div>
        <a href="{{ route('admin.etudiants.create') }}" class="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-xl shadow-indigo-100 gap-3 group">
            <svg class="w-5 h-5 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/>
            </svg>
            Inscrire un Étudiant
        </a>
    </div>

    <div class="mb-6 flex gap-4">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
            </span>
            <input type="text" placeholder="Rechercher un étudiant par nom ou matricule..." 
                class="w-full pl-12 pr-6 py-4 bg-white border-none focus:ring-2 focus:ring-indigo-500 rounded-[1.5rem] shadow-sm font-bold text-sm text-slate-600">
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-8 py-6">Identité & Matricule</th>
                        <th class="px-8 py-6">État Civil</th>
                        <th class="px-8 py-6">Cursus & Origine</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($etudiants as $etudiant)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[1rem] bg-slate-900 flex items-center justify-center text-white text-sm font-black shadow-lg italic uppercase transform group-hover:scale-110 transition duration-300">
                                    {{ substr($etudiant->nom, 0, 1) }}{{ substr($etudiant->prenom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 leading-none uppercase tracking-tighter">{{ $etudiant->nom }}</p>
                                    <p class="text-xs font-bold text-indigo-500 mt-1 italic">{{ $etudiant->prenom }}</p>
                                    <p class="text-[9px] font-black text-slate-400 mt-1 uppercase tracking-widest">
                                        {{ $etudiant->matricule ?? 'Sans Matricule' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-8 py-5 text-xs text-slate-500 font-bold">
                            <div class="flex flex-col">
                                <span class="text-slate-900 font-black italic">
                                    {{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : 'N/C' }}
                                </span>
                                <span class="text-slate-400 font-medium mt-0.5 uppercase tracking-tighter text-[10px]">
                                    à {{ $etudiant->lieu_naissance ?? 'Libreville' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-8 py-5">
                            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg uppercase tracking-wider mb-1">
                                {{ $etudiant->bac ?? 'Série N/C' }}
                            </span>
                            <p class="text-[10px] text-slate-400 font-bold italic tracking-tight truncate max-w-[150px]">
                                {{ $etudiant->provenance }}
                            </p>
                        </td>

                        <td class="px-8 py-5">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" title="Fiche complète">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2.5"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2.5"/></svg>
                                </a>
                                <a href="{{ route('admin.etudiants.edit', $etudiant) }}" class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round"/></svg>
                                </a>
                                <form action="{{ route('admin.etudiants.destroy', $etudiant) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ? Cette action effacera aussi les notes liées.')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round"/></svg>
                                </div>
                                <p class="text-slate-400 font-bold italic">Aucun étudiant inscrit pour le moment.</p>
                                <a href="{{ route('admin.etudiants.create') }}" class="text-indigo-600 font-black text-xs uppercase tracking-widest mt-2 hover:underline">Commencer l'inscription</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
            {{ $etudiants->links() }}
        </div>
    </div>
</div>
@endsection
>>>>>>> 6f3d284 (Initialisation ERP INPTIC : Sidebar et Layout fonctionnels)
