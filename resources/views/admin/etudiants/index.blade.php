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
                                    <p class="text-[9px] font-black text-slate-400 mt-1 uppercase tracking-widest">{{ $etudiant->matricule ?? 'N/C' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-xs text-slate-500 font-bold">
                            {{ $etudiant->date_naissance ? \Carbon\Carbon::parse($etudiant->date_naissance)->format('d/m/Y') : 'N/C' }}
                        </td>
                        <td class="px-8 py-5">
                            <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg uppercase tracking-wider mb-1">
                                {{ $etudiant->bac ?? 'Série N/C' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-black text-indigo-600">
                             <a href="{{ route('admin.etudiants.show', $etudiant) }}" class="hover:underline">Gérer</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-slate-400 italic">Aucun étudiant trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection