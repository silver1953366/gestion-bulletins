@extends('components.layouts.master')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-in pb-12">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-6xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
                Moyennes <span class="text-cyan-600">Matières</span>
            </h1>
            <p class="text-slate-500 font-bold uppercase text-[10px] tracking-[0.3em] mt-4 ml-1">
                Calculs LP ASUR & DAR (Règle 40/60 + Pénalités)
            </p>
        </div>

        {{-- Zone de Calcul Batch (Le "Moteur") --}}
        <div class="bg-slate-900 p-6 rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-800">
            <form action="{{ route('admin.resultats.matieres.calculer') }}" method="POST" class="flex flex-wrap items-center gap-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Promotion / Classe</label>
                    <select name="classe_id" required class="w-48 bg-slate-800 text-white border-none rounded-xl text-xs font-bold focus:ring-cyan-500">
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-2">Unité de Matière</label>
                    <select name="matiere_id" required class="w-48 bg-slate-800 text-white border-none rounded-xl text-xs font-bold focus:ring-cyan-500">
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="mt-4 px-6 py-3 bg-cyan-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white hover:text-slate-900 transition-all group shadow-lg shadow-cyan-900/20">
                    Calculer la Promotion
                </button>
            </form>
        </div>
    </div>

    {{-- Filtres de consultation --}}
    <div class="mb-8 flex flex-wrap gap-4 items-center">
        <form action="{{ route('admin.resultats.matieres.index') }}" method="GET" class="flex gap-4 items-center bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
            <select name="classe_id" class="border-none bg-slate-50 rounded-xl text-xs font-bold text-slate-600 focus:ring-0">
                <option value="">Toutes les classes</option>
                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>{{ $classe->nom }}</option>
                @endforeach
            </select>
            <button type="submit" class="p-2 bg-slate-900 text-white rounded-xl hover:bg-cyan-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
            </button>
        </form>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl animate-pulse">
            <p class="text-emerald-700 text-[10px] font-black uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tableau des Résultats --}}
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-10 py-7 text-center">Rang</th>
                    <th class="px-10 py-7">Étudiant</th>
                    <th class="px-10 py-7">Matière</th>
                    <th class="px-10 py-7 text-center">Moyenne / 20</th>
                    <th class="px-10 py-7 text-center">Session</th>
                    <th class="px-10 py-7 text-right">Validation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm italic">
                @forelse($resultats as $index => $res)
                    <tr class="hover:bg-slate-50/40 transition-all group">
                        <td class="px-10 py-6 text-center">
                            <span class="text-slate-300 font-black italic">#{{ $index + 1 }}</span>
                        </td>
                        <td class="px-10 py-6">
                            <p class="font-black text-slate-900 uppercase leading-none tracking-tight">{{ $res->etudiant->user->full_name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-1 not-italic">INSCRIPTION : {{ $res->etudiant->matricule ?? '2025-001' }}</p>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-bold text-[10px] uppercase">
                                {{ $res->matiere->nom }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-center">
                            <div class="inline-block px-4 py-2 rounded-2xl {{ $res->moyenne >= 10 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                <span class="text-lg font-black tracking-tighter">
                                    {{ number_format($res->moyenne, 2) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-center">
                            @if($res->utilise_rattrapage)
                                <span class="px-3 py-1 bg-amber-100 text-amber-600 rounded-lg font-black text-[9px] uppercase tracking-widest border border-amber-200">
                                    Rattrapage
                                </span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-lg font-black text-[9px] uppercase tracking-widest border border-slate-200">
                                    Initiale
                                </span>
                            @endif
                        </td>
                        <td class="px-10 py-6 text-right">
                            <span class="text-[10px] font-black {{ $res->isValide() ? 'text-emerald-500' : 'text-rose-400' }} uppercase tracking-tighter">
                                {{ $res->isValide() ? 'ACQUISE' : 'NON ACQUISE' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-10 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                                </div>
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Aucun calcul n'a encore été généré</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100">
            {{ $resultats->links() }}
        </div>
    </div>
</div>
@endsection