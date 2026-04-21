<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-amber-600 italic">Importation massive</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                    Gestion des <span class="text-amber-500">Imports</span>
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in">
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <svg class="w-32 h-32 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zM13 9V3.5L18.5 9H13z"/></svg>
            </div>

            <form action="{{ route('admin.imports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="relative z-10">
                    <h2 class="text-lg font-black text-slate-900 uppercase italic mb-6 tracking-tight">Nouvelle importation de notes</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sélectionner le fichier Excel (.xlsx, .csv)</label>
                            <div class="relative group">
                                <input type="file" name="fichier_excel" required 
                                    class="w-full px-6 py-5 bg-slate-50 border-2 border-dashed border-slate-200 group-hover:border-amber-400 rounded-3xl font-bold text-xs transition-all cursor-pointer file:hidden">
                                <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-slate-400 font-bold text-[10px] uppercase tracking-widest">
                                    Parcourir les fichiers
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="h-[64px] bg-slate-900 text-white rounded-3xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3 group">
                            <span>Lancer l'importation</span>
                            <svg class="w-5 h-5 transform group-hover:-translate-y-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-4">
                        <span class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full text-[9px] font-black text-slate-500 uppercase tracking-tight border border-slate-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> 
                            Colonnes : matricule, code_matiere, note, type
                        </span>
                        <span class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full text-[9px] font-black text-slate-500 uppercase tracking-tight border border-slate-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> 
                            Types acceptés : CC, EXAMEN, RATTRAPAGE
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-10 py-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 uppercase italic text-xs tracking-widest">Historique des transactions</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase italic tracking-widest">Total : {{ $imports->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-10 py-5">Date d'import</th>
                            <th class="px-10 py-5">Nom du fichier</th>
                            <th class="px-10 py-5 text-center">Statut</th>
                            <th class="px-10 py-5">Auteur</th>
                            <th class="px-10 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($imports as $import)
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-10 py-5">
                                <span class="font-black text-slate-900 text-xs italic tracking-tight">
                                    {{ $import->created_at->translatedFormat('d F Y') }}
                                </span>
                                <span class="block text-[10px] text-slate-400 font-medium tracking-widest uppercase mt-0.5">
                                    {{ $import->created_at->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-10 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" /></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-600 truncate max-w-[200px]">{{ basename($import->fichier) }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-5 text-center">
                                @if($import->statut == 'success')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg font-black text-[9px] uppercase tracking-widest">
                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Réussi
                                    </span>
                                @elseif($import->statut == 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg font-black text-[9px] uppercase tracking-widest animate-pulse">
                                        <span class="w-1 h-1 rounded-full bg-amber-500"></span> En cours
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-600 rounded-lg font-black text-[9px] uppercase tracking-widest">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span> Échoué
                                    </span>
                                @endif
                            </td>
                            <td class="px-10 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-900 flex items-center justify-center text-[8px] font-black text-white uppercase">
                                        {{ substr($import->createdBy->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="text-[10px] font-black text-slate-600 uppercase tracking-tight italic">{{ $import->createdBy->name ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-5 text-right">
                                <form action="{{ route('admin.imports.destroy', $import) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet historique ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-300 hover:text-rose-600 transition transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-10 py-20 text-center">
                                <p class="text-slate-400 font-medium italic text-sm">Aucun historique d'importation disponible.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($imports->hasPages())
            <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $imports->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>