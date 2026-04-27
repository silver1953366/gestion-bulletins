<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-[9px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2 italic">
                    <span>Structure Académique</span>
                    <i class="fas fa-chevron-right text-[7px] opacity-50"></i>
                    <span>Offre de Formation</span>
                    <i class="fas fa-chevron-right text-[7px] opacity-50"></i>
                    <span class="text-slate-400">Tronc Commun</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase underline decoration-indigo-100 decoration-8 underline-offset-4">
                    Programmes de Parcours
                </h1>
                <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em] mt-1 italic">
                    Gestion mutualisée des semestres par filière et niveau pédagogique
                </p>
            </div>
            <button @click="$dispatch('open-modal', 'add-semestre')" class="px-6 py-3 bg-slate-900 text-white rounded-[1.2rem] font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg shadow-slate-200 group">
                <i class="fas fa-layer-group mr-2 group-hover:rotate-90 transition-transform"></i> Nouveau Tronc Commun
            </button>
        </div>
    </x-slot>

    <div class="py-6 space-y-8 animate-fade-in" x-data="{ 
        currentSemestre: { id: '', libelle: '', annee_universitaire: '', classe_id: '', ues_count: 0, ues: [], classes_liees: [] },
        
        editSemestre(s) {
            this.currentSemestre = { ...s };
            $dispatch('open-modal', 'edit-semestre');
        },

        showUE(s) {
            this.currentSemestre = s;
            $dispatch('open-modal', 'view-ue');
        },

        showClasses(s, classes) {
            this.currentSemestre = s;
            this.currentSemestre.classes_liees = classes;
            $dispatch('open-modal', 'view-classes');
        },

        confirmDelete(s) {
            this.currentSemestre = s;
            $dispatch('open-modal', 'confirm-deletion');
        }
    }">
        
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-5 py-3 rounded-xl font-bold text-[10px] uppercase tracking-tighter italic shadow-sm mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-3 rounded-xl font-bold text-[10px] uppercase tracking-tighter italic shadow-sm mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 items-start">
            @forelse($semestresGroupes as $filiereNom => $semestres)
                <div class="flex flex-col space-y-6">
                    <div class="flex items-center gap-3 ml-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 shadow-[0_0_10px_rgba(79,70,229,0.4)]"></div>
                        <h2 class="text-[15px] font-black text-slate-800 uppercase italic tracking-tighter leading-none">
                            {{ $filiereNom }}
                        </h2>
                    </div>

                    <div class="space-y-6">
                        @foreach($semestres->groupBy(fn($s) => $s->classe->niveau->code ?? 'N/A') as $niveauCode => $items)
                            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-100 transition-all duration-500 overflow-hidden group/card">
                                
                                <div class="bg-slate-50/50 px-8 py-5 border-b border-slate-100 flex justify-between items-center group-hover/card:bg-indigo-50/30 transition-colors">
                                    <h3 class="text-slate-900 font-black uppercase italic text-[10px] tracking-widest">
                                        Niveau : {{ $niveauCode }}
                                    </h3>
                                    <button @click="showClasses({{ json_encode($items->first()) }}, {{ json_encode($items->first()->getClassesPartageantProgramme()) }})" 
                                            class="text-[8px] font-black text-indigo-500 uppercase tracking-widest bg-white border border-indigo-100 px-3 py-1.5 rounded-full hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-users mr-1"></i> Classes
                                    </button>
                                </div>

                                <div class="divide-y divide-slate-50">
                                    @foreach($items as $s)
                                        <div class="px-8 py-6 hover:bg-slate-50/40 transition flex justify-between items-center group/item">
                                            <div class="flex items-center gap-5">
                                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center shadow-sm font-black text-slate-900 italic text-[10px] group-hover/item:scale-110 transition-transform duration-300">
                                                    {{ strtoupper(substr($s->libelle, 0, 1)) }}{{ substr($s->libelle, -1) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-900 uppercase italic text-[12px] tracking-tight">{{ $s->libelle }}</p>
                                                    <div class="flex items-center gap-2 mt-1.5">
                                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter italic">{{ $s->annee_universitaire }}</span>
                                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                        <button @click="showUE({{ json_encode($s->load('ues')) }})" class="text-[9px] text-indigo-500 font-black uppercase hover:underline decoration-2">
                                                            {{ $s->ues_count }} UE Mutualisées
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex gap-2 opacity-0 group-hover/item:opacity-100 transition-opacity duration-300">
                                                <button @click="editSemestre({{ json_encode($s) }})" class="w-9 h-9 flex items-center justify-center bg-white text-slate-400 rounded-xl border border-slate-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition shadow-sm">
                                                    <i class="fas fa-pen-nib text-[10px]"></i>
                                                </button>
                                                <button @click="confirmDelete({{ json_encode($s) }})" class="w-9 h-9 flex items-center justify-center bg-white text-slate-400 rounded-xl border border-slate-100 hover:bg-rose-500 hover:text-white hover:border-rose-500 transition shadow-sm">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 text-center bg-white rounded-[4rem] border-2 border-dashed border-slate-100">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-layer-group text-2xl text-slate-200"></i>
                    </div>
                    <p class="text-slate-400 font-black uppercase text-[10px] tracking-[0.3em] italic">
                        Aucun programme de parcours configuré pour le moment
                    </p>
                    <button @click="$dispatch('open-modal', 'add-semestre')" class="mt-8 text-indigo-600 font-black text-[9px] uppercase tracking-widest hover:underline">
                        Commencer l'initialisation
                    </button>
                </div>
            @endforelse
        </div>

        <x-modal name="add-semestre" focusable>
            <form action="{{ route('admin.semestres.store') }}" method="POST" class="p-10">
                @csrf
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <i class="fas fa-plus text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">Nouveau Tronc Commun</h2>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Initialiser un parcours complet</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2 col-span-2">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1 italic">Libellé du Semestre</label>
                        <input type="text" name="libelle" placeholder="Ex: Semestre 1" required class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-xs transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1 italic">Filière Cible</label>
                        <select name="filiere_id" required class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-xs transition-all outline-none">
                            <option value="">Choisir...</option>
                            @foreach($filieres as $f)
                                <option value="{{ $f->id }}">{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1 italic">Niveau Cible</label>
                        <select name="niveau_id" required class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-xs transition-all outline-none">
                            <option value="">Choisir...</option>
                            @foreach($niveaux as $n)
                                <option value="{{ $n->id }}">{{ $n->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="annee_universitaire" value="{{ $anneeActive }}">
                </div>

                <div class="flex justify-end items-center gap-8 mt-12 border-t border-slate-100 pt-8">
                    <button type="button" x-on:click="$dispatch('close')" class="font-black text-[9px] uppercase text-slate-400 tracking-widest hover:text-slate-600 transition">Annuler l'opération</button>
                    <button type="submit" class="px-10 py-5 bg-slate-900 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 transition shadow-2xl shadow-slate-200">
                        Enregistrer le parcours
                    </button>
                </div>
            </form>
        </x-modal>

        <x-modal name="edit-semestre" focusable>
            <form :action="'{{ route('admin.semestres.index') }}/' + currentSemestre.id" method="POST" class="p-10">
                @csrf @method('PUT')
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-slate-200">
                        <i class="fas fa-edit text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter leading-none">Mise à jour</h2>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 italic" x-text="'ID: #' + currentSemestre.id"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2 col-span-2">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1 italic">Nom du programme</label>
                        <input type="text" name="libelle" x-model="currentSemestre.libelle" required class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-xs transition-all">
                    </div>
                    
                    <div class="space-y-2 col-span-2">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1 italic">Classe Pivot (Référence)</label>
                        <select name="classe_id" x-model="currentSemestre.classe_id" required class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-2xl font-bold text-xs transition-all">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->nom }} - {{ $c->filiere->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="annee_universitaire" x-model="currentSemestre.annee_universitaire">
                </div>

                <div class="flex justify-end gap-8 mt-12 border-t border-slate-100 pt-8">
                    <button type="button" x-on:click="$dispatch('close')" class="font-black text-[9px] uppercase text-slate-400 tracking-widest">Fermer</button>
                    <button type="submit" class="px-10 py-5 bg-indigo-600 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition shadow-xl">Appliquer les changements</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="view-ue" focusable>
            <div class="p-10">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fas fa-book-open text-indigo-600 text-sm"></i>
                            <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter" x-text="currentSemestre.libelle"></h2>
                        </div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Liste des Unités d'Enseignement mutualisées</p>
                    </div>
                    <div class="bg-indigo-50 px-5 py-2.5 rounded-2xl">
                        <span class="text-indigo-600 font-black text-[12px]" x-text="currentSemestre.ues_count + ' UE'"></span>
                    </div>
                </div>

                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-3 custom-scrollbar">
                    <template x-if="currentSemestre.ues_count > 0">
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="ue in currentSemestre.ues" :key="ue.id">
                                <div class="p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100 flex justify-between items-center group hover:bg-white hover:border-indigo-200 transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="w-2 h-2 rounded-full bg-indigo-400 opacity-40"></div>
                                        <span class="text-[11px] font-black text-slate-700 uppercase italic" x-text="ue.libelle"></span>
                                    </div>
                                    <span class="text-[9px] font-black text-indigo-500 bg-white px-3 py-1 rounded-lg border border-slate-100 shadow-sm" x-text="ue.credits + ' CRÉDITS'"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="currentSemestre.ues_count == 0">
                        <div class="py-20 text-center bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                            <i class="fas fa-ghost text-slate-200 text-3xl mb-4"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase italic tracking-widest">
                                Aucun programme d'UE défini pour ce semestre
                            </p>
                        </div>
                    </template>
                </div>

                <div class="mt-10 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-indigo-600 transition shadow-lg">
                        Terminer la lecture
                    </button>
                </div>
            </div>
        </x-modal>

        <x-modal name="view-classes" focusable>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm shadow-indigo-100 rotate-3 group-hover:rotate-0 transition-transform">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Classes Concernées</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-10 leading-relaxed italic">
                    Toutes ces classes physiques partagent le même programme pédagogique via ce tronc commun.
                </p>
                
                <div class="grid grid-cols-2 gap-4">
                    <template x-for="classe in currentSemestre.classes_liees" :key="classe.id">
                        <div class="p-6 bg-white border-2 border-slate-50 rounded-3xl font-black text-[12px] text-slate-800 uppercase italic shadow-sm hover:border-indigo-100 hover:scale-105 transition-all">
                            <i class="fas fa-graduation-cap text-indigo-300 mr-2"></i>
                            <span x-text="classe.nom"></span>
                        </div>
                    </template>
                </div>

                <button type="button" x-on:click="$dispatch('close')" class="mt-12 font-black text-[10px] uppercase text-slate-400 tracking-widest hover:text-indigo-600 transition">
                    <i class="fas fa-times mr-2"></i> Fermer la liste
                </button>
            </div>
        </x-modal>

        <x-modal name="confirm-deletion" focusable>
            <form :action="'{{ route('admin.semestres.index') }}/' + currentSemestre.id" method="POST" class="p-12 text-center">
                @csrf @method('DELETE')
                <div class="w-24 h-24 bg-rose-50 text-rose-500 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-sm">
                    <i class="fas fa-exclamation-triangle text-4xl animate-pulse"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Suppression Définitive</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase mt-4 mb-10 leading-relaxed italic">
                    Êtes-vous certain de vouloir supprimer le <span x-text="currentSemestre.libelle" class="text-rose-500 underline underline-offset-4 decoration-2"></span> ?<br>
                    <span class="text-[9px] text-slate-400 mt-4 block">Cette action est irréversible et affectera tout le parcours lié.</span>
                </p>
                <div class="flex justify-center gap-6">
                    <button type="button" @click="$dispatch('close')" class="px-8 py-5 font-black text-[10px] uppercase text-slate-400 tracking-widest hover:text-slate-600 transition">Annuler</button>
                    <button type="submit" 
                            :disabled="currentSemestre.ues_count > 0" 
                            class="px-12 py-5 bg-rose-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-rose-200 disabled:opacity-20 hover:bg-rose-600 transition-all">
                        Confirmer la suppression
                    </button>
                </div>
                <template x-if="currentSemestre.ues_count > 0">
                    <p class="mt-6 text-[8px] font-black text-rose-400 uppercase italic">
                        Impossible : Supprimez d'abord les UE liées à ce semestre.
                    </p>
                </template>
            </form>
        </x-modal>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        
        .animate-fade-in { animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
    </style>
</x-app-layout>