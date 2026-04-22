@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-3xl bg-slate-900 flex items-center justify-center text-white text-xl font-black italic shadow-xl">
                {{ substr($etudiant->nom, 0, 1) }}{{ substr($etudiant->prenom, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
                    {{ $etudiant->nom }}
                </h1>
                <p class="text-lg font-bold text-indigo-500 italic">{{ $etudiant->prenom }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.etudiants.edit', $etudiant) }}" class="px-8 py-4 bg-white border border-slate-200 text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition shadow-sm">
                Modifier le profil
            </a>
            <a href="{{ route('admin.etudiants.index') }}" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg">
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Card : Infos Académiques --}}
        <div class="md:col-span-1 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm space-y-6">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cursus Actuel</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="text-[9px] font-black text-indigo-500 uppercase">Matricule</label>
                    <p class="text-lg font-black text-slate-900 italic tracking-tighter">{{ $etudiant->matricule ?? 'NON GÉNÉRÉ' }}</p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-indigo-500 uppercase">Série Bac</label>
                    <p class="font-bold text-slate-700">{{ $etudiant->bac ?? 'N/C' }}</p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-indigo-500 uppercase">Provenance</label>
                    <p class="font-bold text-slate-700 uppercase text-xs">{{ $etudiant->provenance ?? 'N/C' }}</p>
                </div>
            </div>
        </div>

        {{-- Card : État Civil --}}
        <div class="md:col-span-2 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Informations État Civil</h3>
            
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Date de naissance</span>
                    <p class="font-bold text-slate-900">
                        {{ $etudiant->date_naissance ? \Carbon\Carbon::parse($etudiant->date_naissance)->format('d F Y') : 'Non renseignée' }}
                    </p>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Lieu de naissance</span>
                    <p class="font-bold text-slate-900">{{ $etudiant->lieu_naissance ?? 'Non renseigné' }}</p>
                </div>
            </div>

            <div class="mt-12 p-6 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Note administrative</p>
                <p class="text-xs text-slate-500 italic">Dossier académique complet pour l'année 2025-2026. L'étudiant est rattaché à la promotion LP ASUR.</p>
            </div>
        </div>
    </div>
</div>
@endsection