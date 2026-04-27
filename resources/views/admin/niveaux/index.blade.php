<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[9px] font-black uppercase tracking-[0.2em]">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-indigo-600 italic">Niveaux</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">Échelons <span class="text-indigo-600 underline decoration-indigo-100 underline-offset-4">Académiques</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-niveau')" class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-indigo-50 gap-3 group">
                <svg class="w-4 h-4 transform group-hover:rotate-12 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Nouveau Niveau
            </button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6" x-data="{ 
        currentNiveau: { id: '', code: '', classes_count: 0, classes: [] }, 
        editAction: '',
        deleteAction: '',
        
        openClasses(niveau) {
            this.currentNiveau = niveau;
            $dispatch('open-modal', 'view-classes');
        },
        openEdit(niveau) {
            this.currentNiveau = niveau;
            this.editAction = '/admin/niveaux/' + niveau.id;
            $dispatch('open-modal', 'edit-niveau');
        },
        openDelete(niveau) {
            this.currentNiveau = niveau;
            this.deleteAction = '/admin/niveaux/' + niveau.id;
            $dispatch('open-modal', 'confirm-delete-niveau');
        }
    }">
        
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-[10px] font-bold text-emerald-700 uppercase tracking-widest shadow-sm animate-fade-in-down">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-5">Code de l'échelon</th>
                        <th class="px-10 py-5">Classes rattachées</th>
                        <th class="px-10 py-5 text-right">Gestion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($niveaux as $niveau)
                    <tr class="hover:bg-indigo-50/20 transition-colors group">
                        <td class="px-10 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-black text-[10px]">
                                    {{ substr($niveau->code, 0, 2) }}
                                </div>
                                <span class="font-black text-slate-800 uppercase italic tracking-tighter">{{ $niveau->code }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-5">
                            @if($niveau->classes_count > 0)
                                <button @click="openClasses({{ $niveau->toJson() }})" class="inline-flex items-center px-3 py-1.5 rounded-full bg-slate-900 text-white text-[9px] font-black uppercase tracking-tight hover:bg-indigo-600 transition-all gap-2 group/btn">
                                    {{ $niveau->classes_count }} structure(s) active(s)
                                    <svg class="w-3 h-3 text-indigo-400 group-hover/btn:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            @else
                                <span class="text-slate-300 italic text-[10px] font-medium tracking-widest uppercase">Aucune dépendance</span>
                            @endif
                        </td>
                        <td class="px-10 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <button @click="openEdit({{ $niveau->toJson() }})" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 113.536 3.536L12 20.414H8v-4L19.586 3.586z" stroke-width="2" /></svg>
                                </button>
                                
                                <button @click="openDelete({{ $niveau->toJson() }})" class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-20 text-center uppercase tracking-[0.3em] font-black text-slate-200 text-xs italic">
                            Le registre des niveaux est vide
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL : LISTE DES CLASSES RATTACHÉES --}}
        <x-modal name="view-classes" focusable>
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">Classes liées à <span class="text-indigo-600" x-text="currentNiveau.code"></span></h2>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Aperçu de la structure académique</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="classe in currentNiveau.classes" :key="classe.id">
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl group hover:bg-indigo-600 transition-all cursor-default">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 group-hover:text-indigo-200 uppercase tracking-widest mb-1" x-text="classe.annee_universitaire"></span>
                                <span class="text-sm font-black text-slate-800 group-hover:text-white uppercase italic tracking-tighter" x-text="classe.nom"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-8 py-3.5 bg-slate-100 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Fermer l'aperçu
                    </button>
                </div>
            </div>
        </x-modal>

        {{-- MODAL : SUPPRESSION --}}
        <x-modal name="confirm-delete-niveau" focusable>
            <div class="p-8 text-center bg-white">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-3xl flex items-center justify-center mx-auto mb-6 transform -rotate-6 border border-rose-100 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                </div>
                
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-4">
                    Supprimer <span class="text-rose-600" x-text="currentNiveau.code"></span> ?
                </h2>
                
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-relaxed mb-8 max-w-sm mx-auto">
                    Cette action est irréversible. Toutes les données liées à cet échelon seront définitivement effacées du système.
                </p>
                
                <template x-if="currentNiveau.classes_count > 0">
                    <div class="mb-8 p-4 bg-slate-900 rounded-2xl border border-slate-800 text-left">
                        <div class="flex items-start gap-4">
                            <span class="shrink-0 w-6 h-6 bg-amber-500 text-slate-900 rounded-full flex items-center justify-center font-black text-[10px]">!</span>
                            <div>
                                <h4 class="text-[10px] font-black text-white uppercase tracking-widest mb-1 font-italic">Suppression Bloquée</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">
                                    Cet échelon possède <span class="text-amber-400" x-text="currentNiveau.classes_count"></span> classe(s). Veuillez d'abord supprimer ou déplacer ces classes.
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="flex items-center justify-center gap-4">
                    <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors uppercase font-black">
                        Annuler
                    </button>
                    
                    <form :action="deleteAction" method="POST" x-show="currentNiveau.classes_count == 0" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-6 py-4 bg-rose-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-rose-100">
                            Confirmer
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- MODAL : AJOUT --}}
        <x-modal name="add-niveau" focusable>
            <div class="p-10">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">Nouveau Niveau</h2>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Registre scolarité</span>
                    </div>
                </div>

                <form action="{{ route('admin.niveaux.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Désignation du code</label>
                        <input type="text" name="code" required maxlength="10" placeholder="Ex: LICENCE 1" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl font-black text-sm focus:ring-0 focus:border-indigo-600 focus:bg-white transition-all uppercase placeholder:text-slate-300">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-50">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODAL : ÉDITION --}}
        <x-modal name="edit-niveau" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8 leading-none">Rectifier <span class="text-indigo-600">l'échelon</span></h2>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Code révisé</label>
                        <input type="text" name="code" x-model="currentNiveau.code" required maxlength="10" class="w-full px-6 py-4 bg-slate-50 border-2 border-transparent rounded-2xl font-black text-sm focus:ring-0 focus:border-indigo-600 focus:bg-white transition-all uppercase">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Retour</button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-indigo-100">Appliquer</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>