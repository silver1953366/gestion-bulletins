@extends('components.layouts.master')

@section('content')
<div class="max-w-7xl mx-auto animate-fade-in pb-12">
    {{-- Header de la page --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-6xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
                Corps <span class="text-cyan-600">Enseignant</span>
            </h1>
            <p class="text-slate-500 font-bold uppercase text-[10px] tracking-[0.3em] mt-4 ml-1">
                Gestion académique & spécialisations
            </p>
        </div>

        <div class="flex items-center gap-4">
            {{-- Statistiques rapides --}}
            <div class="hidden lg:flex items-center gap-4 mr-6 border-r border-slate-200 pr-6">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</p>
                    <p class="text-2xl font-black text-slate-900 leading-none">{{ $teachers->total() }}</p>
                </div>
            </div>

            {{-- Bouton de création --}}
            <a href="{{ route('admin.teachers.create') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-cyan-600 transition-all shadow-2xl shadow-cyan-100 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/>
                </svg>
                Nouveau Profil
            </a>
        </div>
    </div>

    {{-- Alertes de succès/erreur --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl animate-bounce">
            <p class="text-emerald-700 text-xs font-black uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tableau des enseignants --}}
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                    <th class="px-10 py-7">Identité & Contact</th>
                    <th class="px-10 py-7">Spécialité</th>
                    <th class="px-10 py-7">Grade</th>
                    <th class="px-10 py-7">Matières</th>
                    <th class="px-10 py-7 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm italic">
                @forelse($teachers as $teacher)
                    <tr class="hover:bg-slate-50/40 transition-all group">
                        {{-- Infos Perso --}}
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-black text-sm uppercase shadow-sm group-hover:scale-110 transition-transform">
                                    {{ substr($teacher->user->first_name ?? 'T', 0, 1) }}{{ substr($teacher->user->last_name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-900 uppercase leading-none tracking-tight">
                                        {{ $teacher->user->full_name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 not-italic">{{ $teacher->user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Spécialité --}}
                        <td class="px-10 py-6 text-xs font-black text-slate-700 uppercase tracking-tighter">
                            {{ $teacher->specialite }}
                        </td>

                        {{-- Grade --}}
                        <td class="px-10 py-6">
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-500 rounded-full font-black text-[9px] uppercase tracking-widest">
                                {{ $teacher->grade ?? 'Non défini' }}
                            </span>
                        </td>

                        {{-- Nombre de matières --}}
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-2 text-cyan-600 font-black text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2.5"/></svg>
                                {{ $teacher->matieres->count() }} cours
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-10 py-6 text-right">
                            <div class="flex justify-end gap-1">
                                {{-- Voir --}}
                                <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                   class="p-3 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Voir les détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2"/></svg>
                                </a>

                                {{-- Modifier --}}
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                                   class="p-3 text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition-all" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </a>

                                {{-- Supprimer --}}
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" 
                                      onsubmit="return confirm('Attention ! Vous allez retirer le statut d\'enseignant à cet utilisateur. Continuer ?')" class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="p-3 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" stroke-width="1.5"/></svg>
                                <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">Aucun enseignant répertorié</p>
                                <a href="{{ route('admin.teachers.create') }}" class="mt-4 text-cyan-600 font-black text-xs uppercase underline">Créer le premier profil</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
            {{ $teachers->links() }}
        </div>
    </div>
</div>
@endsection