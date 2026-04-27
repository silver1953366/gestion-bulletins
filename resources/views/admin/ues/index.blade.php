<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[9px] font-black uppercase tracking-[0.2em]">
                        <li class="text-slate-400">Architecture</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-emerald-500 italic">Unités d'Enseignement</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase leading-none">
                    Composantes <span class="text-emerald-500 underline decoration-emerald-50 decoration-4 underline-offset-4">Pédagogiques</span>
                </h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-ue')" class="inline-flex items-center justify-center px-6 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-4 h-4 transform group-hover:rotate-90 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Nouvelle Unité (UE)
            </button>
        </div>
    </x-slot>

    <div class="space-y-6 animate-fade-in" 
        x-data="{ 
            search: '',
            filterSemestre: '',
            allUes: {{ $ues->getCollection()->map(function($ue) {
                return [
                    'id' => $ue->id,
                    'code' => $ue->code,
                    'libelle' => $ue->libelle,
                    'semestre_id' => $ue->semestre_id,
                    'matieres_count' => $ue->matieres_count ?? 0,
                    'total_coeff' => (int)$ue->total_matieres_coeff,
                    'total_credits' => (int)$ue->total_matieres_credits,
                    'filiere_nom' => $ue->semestre->classe->filiere->nom ?? 'N/A',
                    'niveau_code' => $ue->semestre->classe->niveau->code ?? '?',
                    'semestre_libelle' => $ue->semestre->libelle ?? '?'
                ];
            })->toJson() }},
            currentUe: { id: '', code: '', libelle: '', semestre_id: '' },
            
            get filteredUes() {
                return this.allUes.filter(ue => {
                    const matchesSearch = ue.code.toLowerCase().includes(this.search.toLowerCase()) || 
                                          ue.libelle.toLowerCase().includes(this.search.toLowerCase());
                    const matchesSemestre = this.filterSemestre === '' || ue.semestre_id == this.filterSemestre;
                    return matchesSearch && matchesSemestre;
                });
            },

            openEdit(ue) {
                this.currentUe = { ...ue };
                $dispatch('open-modal', 'edit-ue');
            },
            
            openDelete(ue) {
                this.currentUe = ue;
                $dispatch('open-modal', 'confirm-delete');
            }
        }">
        
        {{-- Barre de recherche & Filtres --}}
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <input type="text" x-model="search" placeholder="Rechercher une UE..." 
                    class="w-full pl-12 pr-6 py-4 bg-white border-none rounded-2xl shadow-sm font-bold text-xs focus:ring-2 focus:ring-emerald-500 transition-all placeholder:text-slate-300">
                <svg class="w-4 h-4 text-slate-300 absolute left-5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" stroke-linecap="round"/></svg>
            </div>
            
            <select x-model="filterSemestre" class="w-full md:w-64 px-6 py-4 bg-white border-none rounded-2xl shadow-sm font-black text-[9px] uppercase tracking-widest text-slate-500">
                <option value="">Tous les semestres</option>
                @foreach($semestres as $s)
                    <option value="{{ $s->id }}">{{ $s->classe->filiere->nom }} - {{ $s->libelle }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Code & Désignation</th>
                        <th class="px-10 py-6">Parcours</th>
                        <th class="px-10 py-6 text-center">Matières</th>
                        <th class="px-10 py-6 text-center">Poids Total (Matières)</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    <template x-for="ue in filteredUes" :key="ue.id">
                        <tr class="hover:bg-slate-50/50 transition group">
                            <td class="px-10 py-5">
                                <div class="flex flex-col">
                                    <span class="text-emerald-500 font-black text-[9px] uppercase tracking-widest italic mb-1" x-text="ue.code"></span>
                                    <span class="font-bold text-slate-800 uppercase tracking-tight text-[11px]" x-text="ue.libelle"></span>
                                </div>
                            </td>
                            <td class="px-10 py-5">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-900 uppercase italic" x-text="ue.filiere_nom"></span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="ue.niveau_code + ' — ' + ue.semestre_libelle"></span>
                                </div>
                            </td>
                            <td class="px-10 py-5 text-center">
                                <span class="text-[9px] font-black px-3 py-1.5 bg-slate-100 text-slate-600 rounded-full uppercase italic" x-text="ue.matieres_count + ' élements'"></span>
                            </td>
                            <td class="px-10 py-5 text-center">
                                <div class="inline-flex items-center gap-3">
                                    <span class="text-slate-900 font-black text-[11px]" x-text="'Σ Coeff: ' + ue.total_coeff"></span>
                                    <span class="w-px h-3 bg-slate-200"></span>
                                    <span class="text-emerald-500 font-black text-[10px] italic" x-text="ue.total_credits + ' ECTS'"></span>
                                </div>
                            </td>
                            <td class="px-10 py-5 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEdit(ue)" class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/></svg>
                                    </button>
                                    <button @click="openDelete(ue)" class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
                
                {{-- FOOTER : SOMMES TOTALES --}}
                <tfoot class="bg-slate-900 text-white">
                    <tr>
                        <td colspan="3" class="px-10 py-5 text-[10px] font-black uppercase tracking-widest italic">
                            Total de la sélection <span class="ml-2 text-slate-500" x-text="'(' + filteredUes.length + ' UEs)'"></span>
                        </td>
                        <td class="px-10 py-5 text-center">
                            <div class="inline-flex items-center gap-3">
                                <span class="font-black text-[11px]" x-text="'Σ Coeff: ' + filteredUes.reduce((sum, ue) => sum + parseInt(ue.total_coeff || 0), 0)"></span>
                                <span class="w-px h-3 bg-slate-700"></span>
                                <span class="text-emerald-400 font-black text-[10px] italic" x-text="filteredUes.reduce((sum, ue) => sum + parseInt(ue.total_credits || 0), 0) + ' ECTS'"></span>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $ues->links() }}
        </div>

        {{-- MODALE : CRÉATION --}}
        <x-modal name="add-ue" focusable>
            <div class="p-10">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Nouveau <span class="text-emerald-500">Bloc UE</span></h2>
                <form action="{{ route('admin.ues.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Code</label>
                            <input type="text" name="code" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Semestre</label>
                            <select name="semestre_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                                @foreach($semestres as $s)
                                    <option value="{{ $s->id }}">{{ $s->classe->filiere->nom }} - {{ $s->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Libellé de l'UE</label>
                        <input type="text" name="libelle" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-50">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[9px] uppercase text-slate-400 hover:text-slate-600 transition">Annuler</button>
                        <button type="submit" class="px-10 py-4 bg-emerald-500 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-emerald-100">Enregistrer l'UE</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE : ÉDITION --}}
        <x-modal name="edit-ue" focusable>
            <div class="p-10">
                <h2 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Édition <span class="text-emerald-500" x-text="currentUe.code"></span></h2>
                <form :action="'/admin/ues/' + currentUe.id" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Code</label>
                            <input type="text" name="code" x-model="currentUe.code" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs uppercase">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Semestre</label>
                            <select name="semestre_id" x-model="currentUe.semestre_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                                @foreach($semestres as $s)
                                    <option value="{{ $s->id }}">{{ $s->classe->filiere->nom }} - {{ $s->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic ml-1">Libellé</label>
                        <input type="text" name="libelle" x-model="currentUe.libelle" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                    </div>
                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-50">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[9px] uppercase text-slate-400">Abandonner</button>
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-slate-100">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE : SUPPRESSION --}}
        <x-modal name="confirm-delete" focusable>
             <div class="p-12 text-center">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Supprimer l'UE ?</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight italic mb-10 leading-relaxed">
                    Toutes les matières rattachées à <span class="text-rose-500 font-black" x-text="currentUe.code"></span> seront également impactées.
                </p>
                <form :action="'/admin/ues/' + currentUe.id" method="POST" class="flex justify-center gap-3">
                    @csrf @method('DELETE')
                    <button type="button" @click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[9px] uppercase text-slate-400">Annuler</button>
                    <button type="submit" class="px-10 py-4 bg-rose-600 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest shadow-xl shadow-rose-100">Confirmer la suppression</button>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>