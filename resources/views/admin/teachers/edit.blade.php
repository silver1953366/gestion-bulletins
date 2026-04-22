@extends('components.layouts.master')

@section('content')
<div class="max-w-4xl mx-auto animate-fade-in pb-12">
    {{-- Header --}}
    <div class="mb-10">
        <a href="{{ route('admin.teachers.index') }}" class="group inline-flex items-center gap-2 text-slate-400 font-black text-xs uppercase tracking-[0.2em] mb-4 hover:text-cyan-600 transition-all">
            <svg class="w-5 h-5 transform group-hover:-translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Annuler les modifications
        </a>
        <h1 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
            Modifier <span class="text-cyan-600">Profil</span>
        </h1>
        <p class="mt-2 text-slate-500 font-bold uppercase text-[10px] tracking-widest">Enseignant : {{ $teacher->user->full_name }}</p>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" class="p-8 md:p-12 space-y-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Spécialité --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Spécialité</label>
                    <input type="text" name="specialite" value="{{ old('specialite', $teacher->specialite) }}" required
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-cyan-500 focus:bg-white rounded-2xl font-bold transition-all">
                </div>

                {{-- Grade --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Grade Académique</label>
                    <input type="text" name="grade" value="{{ old('grade', $teacher->grade) }}"
                        class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent focus:border-cyan-500 focus:bg-white rounded-2xl font-bold transition-all">
                </div>
            </div>

            {{-- Matières avec pré-sélection --}}
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Matières assignées</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-slate-50 p-6 rounded-[2rem]">
                    @foreach($matieres as $matiere)
                        <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-cyan-500 transition-all group">
                            <input type="checkbox" name="matieres[]" value="{{ $matiere->id }}" 
                                {{ $teacher->matieres->contains($matiere->id) ? 'checked' : '' }}
                                class="w-5 h-5 rounded text-cyan-600 focus:ring-cyan-500 border-slate-200">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-cyan-600 uppercase">{{ $matiere->nom }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-3xl font-black uppercase text-xs tracking-[0.2em] hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-100">
                    Mettre à jour le profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection