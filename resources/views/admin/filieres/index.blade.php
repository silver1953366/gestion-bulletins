<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[9px] font-black uppercase tracking-[0.2em]">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-emerald-600 italic">Filières</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase">Offre <span class="text-emerald-600 underline decoration-emerald-100 underline-offset-4">Académique</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-filiere')" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-emerald-50 gap-3 group">
                <svg class="w-4 h-4 transform group-hover:rotate-12 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Ajouter une Filière
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in" x-data="{ 
        currentFiliere: { id: '', nom: '', departement_id: '', classes_count: 0 }, 
        editAction: '',
        deleteAction: '',
        openEdit(filiere) {
            this.currentFiliere = filiere;
            this.editAction = '/admin/filieres/' + filiere.id;
            $dispatch('open-modal', 'edit-filiere');
        },
        openDelete(filiere) {
            this.currentFiliere = filiere;
            this.deleteAction = '/admin/filieres/' + filiere.id;
            $dispatch('open-modal', 'confirm-delete-filiere');
        }
    }">
        
        {{-- Alertes --}}
        @if(session('success'))
            <div class="p-3 bg-emerald-50 border-l-2 border-emerald-500 rounded-r-lg text-[10px] font-bold text-emerald-700 uppercase tracking-wider">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[8px] font-black text-slate-400 uppercase tracking-[0.25em] border-b border-slate-100">
                        <th class="px-8 py-4">Spécialité</th>
                        <th class="px-8 py-4">Rattachement</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                    @forelse($filieres as $filiere)
                    <tr class="hover:bg-slate-50/30 transition group">
                        <td class="px-8 py-4">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-900 text-sm uppercase italic tracking-tighter">{{ $filiere->nom }}</span>
                                <span class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">REF: FIL-{{ $filiere->id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg font-black text-[9px] uppercase tracking-tighter border border-emerald-100 italic">
                                {{ $filiere->departement->nom ?? 'Indépendant' }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                <button @click="openEdit({{ json_encode($filiere) }})" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <button @click="openDelete({{ json_encode($filiere) }})" class="p-2 text-slate-300 hover:text-rose-600 hover:bg-white rounded-lg transition shadow-sm border border-transparent hover:border-slate-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-16 text-center opacity-30 font-black uppercase text-[10px] tracking-[0.4em] italic text-slate-400">Aucune filière disponible</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($filieres->hasPages())
            <div class="px-2">{{ $filieres->links() }}</div>
        @endif

        {{-- MODAL : CONFIRMATION SUPPRESSION --}}
        <x-modal name="confirm-delete-filiere" focusable>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Supprimer la <span class="text-rose-600">Filière</span></h2>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-8 leading-relaxed">
                    Voulez-vous retirer la filière <br>
                    <span class="text-slate-900 font-black italic" x-text="currentFiliere.nom"></span> du catalogue ?
                </p>

                {{-- Blocage si classes présentes --}}
                <div x-show="currentFiliere.classes_count > 0" class="p-4 bg-rose-50 rounded-xl border border-rose-100 mb-8">
                    <p class="text-[9px] font-black text-rose-700 uppercase tracking-widest leading-tight">
                        Impossible : <span x-text="currentFiliere.classes_count"></span> classe(s) dépendent de cette filière.<br>
                        Videz la filière avant de la supprimer.
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-8 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Abandonner</button>
                    
                    <form :action="deleteAction" method="POST" x-show="currentFiliere.classes_count == 0">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-8 py-3 bg-rose-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">
                            Confirmer
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- MODAL : AJOUT --}}
        <x-modal name="add-filiere" focusable>
            <div class="p-8">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-6">Nouvelle <span class="text-emerald-600 italic">Filière</span></h2>
                
                <form action="{{ route('admin.filieres.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="nom" required placeholder="Ex: Maintenance Industrielle" class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Département</label>
                        <select name="departement_id" required class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all cursor-pointer">
                            <option value="">Choisir l'unité parente...</option>
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="px-8 py-3 bg-emerald-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-emerald-50">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODAL : ÉDITION --}}
        <x-modal name="edit-filiere" focusable>
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Éditer la <span class="text-emerald-600 italic">Filière</span></h2>
                    <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[8px] font-black" x-text="'REF: ' + currentFiliere.id"></span>
                </div>
                
                <form :action="editAction" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="nom" x-model="currentFiliere.nom" required class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Département</label>
                        <select name="departement_id" x-model="currentFiliere.departement_id" required class="w-full px-5 py-3.5 bg-slate-50 border-transparent rounded-xl font-bold text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all cursor-pointer">
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Fermer</button>
                        <button type="submit" class="px-8 py-3 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-slate-100">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>