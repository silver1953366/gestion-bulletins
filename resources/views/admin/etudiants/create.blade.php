@extends('components.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in pb-12">
    {{-- Header --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <a href="{{ route('admin.etudiants.index') }}" class="group inline-flex items-center gap-2 text-indigo-600 font-black text-xs uppercase tracking-[0.2em] mb-4 hover:text-slate-900 transition-all">
                <svg class="w-5 h-5 transform group-hover:-translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Retour
            </a>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
                Nouvel <span class="text-indigo-600">Étudiant</span>
            </h1>
        </div>

        {{-- Affichage des erreurs système --}}
        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm animate-bounce">
                <p class="text-rose-700 text-xs font-black uppercase">Erreur : {{ session('error') }}</p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <form action="{{ route('admin.etudiants.store') }}" method="POST" class="p-8 md:p-12 space-y-10">
            @csrf

            {{-- BLOC 1 : IDENTITÉ --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="ESSONE"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold uppercase transition-all">
                    @error('nom') <span class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Marc"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold transition-all">
                    @error('prenom') <span class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- BLOC 2 : ACCÈS SYSTÈME --}}
            <div class="p-8 bg-indigo-50/50 rounded-[2rem] grid grid-cols-1 md:grid-cols-2 gap-8 border border-indigo-100/50">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-4">Email Institutionnel</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="m.essone@inptic.ga"
                        class="w-full px-6 py-4 bg-white border-2 border-transparent focus:border-indigo-500 rounded-2xl font-bold transition-all">
                    @error('email') <span class="text-rose-500 text-[10px] font-bold ml-4">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-indigo-400 uppercase tracking-widest ml-4">Mot de passe par défaut</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-6 py-4 bg-white border-2 border-transparent focus:border-indigo-500 rounded-2xl font-bold transition-all">
                </div>
            </div>

            {{-- BLOC 3 : INFOS ACADÉMIQUES --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Date de Naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 rounded-2xl font-bold transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Série Bac</label>
                    <input type="text" name="bac" value="{{ old('bac') }}" placeholder="TI / S / ES"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 rounded-2xl font-bold transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Provenance</label>
                    <input type="text" name="provenance" value="{{ old('provenance') }}" placeholder="Lycée..."
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 rounded-2xl font-bold transition-all">
                </div>
            </div>

            {{-- BOUTONS --}}
            <div class="pt-6 flex gap-4">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-6 rounded-3xl font-black uppercase text-xs tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
                    Enregistrer et Créer le matricule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection