@extends('components.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in pb-12">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <a href="{{ route('admin.teachers.index') }}" class="group inline-flex items-center gap-2 text-slate-400 font-black text-xs uppercase tracking-[0.2em] mb-4 hover:text-cyan-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round"/></svg>
                Retour
            </a>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
                Fiche <span class="text-cyan-600">Enseignant</span>
            </h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="px-6 py-3 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-cyan-600 transition shadow-lg">
                Modifier
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Colonne Gauche : Identité --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center text-3xl font-black italic border-4 border-white shadow-xl">
                    {{ substr($teacher->user->first_name, 0, 1) }}{{ substr($teacher->user->last_name, 0, 1) }}
                </div>
                <h2 class="text-xl font-black text-slate-900 uppercase italic leading-tight">{{ $teacher->user->full_name }}</h2>
                <p class="text-slate-400 font-bold text-xs mt-1">{{ $teacher->user->email }}</p>
                <div class="mt-6 pt-6 border-t border-slate-50 flex justify-around">
                    <div>
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Grade</p>
                        <p class="font-bold text-slate-700 italic">{{ $teacher->grade ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne Droite : Spécialité & Matières --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                    <span class="w-8 h-[2px] bg-cyan-500"></span>
                    Domaine d'expertise
                </h3>
                
                <div class="mb-10">
                    <p class="text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-2">Spécialité Principale</p>
                    <p class="text-3xl font-black text-slate-900 italic uppercase leading-none">{{ $teacher->specialite }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Matières prises en charge :</p>
                    <div class="flex flex-wrap gap-3">
                        @forelse($teacher->matieres as $matiere)
                            <span class="px-5 py-3 bg-slate-50 text-slate-700 rounded-2xl font-black text-[10px] uppercase border border-slate-100 shadow-sm">
                                {{ $matiere->nom }}
                            </span>
                        @empty
                            <p class="text-slate-400 text-sm italic font-medium">Aucune matière assignée pour le moment.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection