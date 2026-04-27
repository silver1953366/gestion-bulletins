<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[9px] font-black uppercase tracking-[0.2em]">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-sky-600 italic">Départements</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase">Unités <span class="text-sky-600 underline decoration-sky-100 underline-offset-4">Académiques</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-dept')" class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-sky-600 transition-all shadow-lg shadow-slate-200 gap-3 group">
                <svg class="w-4 h-4 transform group-hover:rotate-12 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Nouveau Département
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        currentDept: { id: '', nom: '', filieres_count: 0, filieres: [] }, 
        editAction: '',
        deleteAction: '',
        
        openEdit(dept) {
            this.currentDept = dept;
            this.editAction = '/admin/departements/' + dept.id;
            $dispatch('open-modal', 'edit-dept');
        },
        openShow(dept) {
            this.currentDept = dept;
            $dispatch('open-modal', 'show-dept');
        },
        openDelete(dept) {
            this.currentDept = dept;
            this.deleteAction = '/admin/departements/' + dept.id;
            $dispatch('open-modal', 'confirm-delete');
        }
    }">
        
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="p-3 bg-emerald-50 border-l-2 border-emerald-500 rounded-r-lg text-[10px] font-bold text-emerald-700 uppercase tracking-wider">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-rose-50 border-l-2 border-rose-500 rounded-r-lg text-[10px] font-bold text-rose-700 uppercase tracking-wider">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[8px] font-black text-slate-400 uppercase tracking-[0.25em] border-b border-slate-100">
                        <th class="px-8 py-4">Structure</th>
                        <th class="px-8 py-4">Offre Académique</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                    @forelse($departements as $dept)
                    <tr class="hover:bg-slate-50/30 transition group">
                        <td class="px-8 py-4">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-900 text-sm uppercase italic tracking-tighter">{{ $dept->nom }}</span>
                                <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">ID: #{{ str_pad($dept->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <button @click="openShow({{ json_encode($dept) }})" 
                                    class="inline-flex items-center px-3 py-1 bg-sky-50 text-sky-600 rounded-lg font-black text-[9px] uppercase tracking-tighter hover:bg-sky-600 hover:text-white transition-all group/btn">
                                <svg class="w-3 h-3 mr-1.5 opacity-60 group-hover/btn:scale-110" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                                {{ $dept->filieres_count }} Filière(s)
                            </button>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                <button @click="openEdit({{ json_encode($dept) }})" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <button @click="openDelete({{ json_encode($dept) }})" class="p-2 text-slate-300 hover:text-rose-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center opacity-30 font-black uppercase text-[10px] tracking-[0.4em] italic text-slate-400">Aucun département enregistré</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($departements->hasPages())
            <div class="px-2">{{ $departements->links() }}</div>
        @endif

        {{-- MODAL : CONFIRMATION SUPPRESSION --}}
        <x-modal name="confirm-delete" focusable>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Confirmer la <span class="text-rose-600">Suppression</span></h2>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide leading-relaxed mb-8">
                    Êtes-vous sûr de vouloir supprimer le département <br>
                    <span class="text-slate-900 font-black italic" x-text="currentDept.nom"></span> ?
                </p>

                {{-- Logique d'affichage selon présence de filières --}}
                <div x-show="currentDept.filieres_count > 0" class="p-4 bg-amber-50 rounded-xl border border-amber-100 mb-8">
                    <p class="text-[9px] font-black text-amber-700 uppercase tracking-widest leading-tight">
                        Action Impossible : Ce département possède encore <span x-text="currentDept.filieres_count"></span> filière(s).<br>
                        Veuillez les détacher ou les supprimer d'abord.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-8 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                    
                    <form :action="deleteAction" method="POST" x-show="currentDept.filieres_count == 0">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">
                            Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- MODAL : VOIR FILIÈRES --}}
        <x-modal name="show-dept" focusable>
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter" x-text="currentDept.nom"></h2>
                        <p class="text-[9px] font-black text-sky-600 uppercase tracking-widest mt-1">Liste des filières rattachées</p>
                    </div>
                    <button x-on:click="$dispatch('close')" class="p-2 bg-slate-50 text-slate-400 hover:text-rose-500 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    <template x-for="filiere in currentDept.filieres" :key="filiere.id">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center gap-3 group transition hover:bg-white hover:shadow-md">
                            <div class="w-1.5 h-1.5 bg-sky-400 rounded-full group-hover:scale-150 transition"></div>
                            <span class="text-[10px] font-bold text-slate-700 uppercase" x-text="filiere.nom"></span>
                        </div>
                    </template>
                    <template x-if="currentDept.filieres && currentDept.filieres.length === 0">
                        <div class="col-span-2 py-10 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <p class="text-[9px] font-black text-slate-400 uppercase italic tracking-[0.2em]">Aucune filière alouée</p>
                        </div>
                    </template>
                </div>
            </div>
        </x-modal>

        {{-- MODAL AJOUT --}}
        <x-modal name="add-dept" focusable>
            <div class="p-8">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Ajouter une <span class="text-sky-600 italic">Unité</span></h2>
                <form action="{{ route('admin.departements.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nomination</label>
                        <input type="text" name="nom" required placeholder="Ex: SCIENCES DE GESTION" class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-sky-600 focus:bg-white transition-all placeholder:text-slate-300">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-sky-600 transition-all shadow-lg shadow-slate-100">Valider</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODAL MODIFICATION --}}
        <x-modal name="edit-dept" focusable>
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Édition <span class="text-sky-600 italic">Structure</span></h2>
                    <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[8px] font-black" x-text="'ID: ' + currentDept.id"></span>
                </div>
                <form :action="editAction" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Désignation</label>
                        <input type="text" name="nom" x-model="currentDept.nom" required class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-sky-600 focus:bg-white transition-all">
                    </div>
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Fermer</button>
                        <button type="submit" class="px-8 py-3 bg-sky-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-sky-100">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</x-app-layout>