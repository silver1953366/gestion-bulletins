<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Configuration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-amber-600 italic">Matières & Unités</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">
                    Catalogue des <span class="text-amber-500">Matières</span>
                </h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-matiere')" class="bg-slate-900 text-white px-8 py-4 rounded-3xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                Ajouter une matière
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        search: '', 
        currentMatiere: {}, 
        editAction: '',
        openEdit(matiere) {
            this.currentMatiere = matiere;
            this.editAction = '/admin/matieres/' + matiere.id;
            $dispatch('open-modal', 'edit-matiere');
        }
    }">
        <div class="bg-white rounded-[2.5rem] p-4 border border-slate-100 shadow-sm">
            <div class="relative">
                <input type="text" x-model="search" placeholder="Rechercher par code ou libellé (ex: ASUR...)" 
                    class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold text-slate-600 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 transition-all">
                <svg class="w-5 h-5 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="px-10 py-6">Code</th>
                            <th class="px-10 py-6">Matière</th>
                            <th class="px-10 py-6">UE d'appartenance</th>
                            <th class="px-10 py-6 text-center">Coef.</th>
                            <th class="px-10 py-6 text-center">Crédits</th>
                            <th class="px-10 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($matieres as $matiere)
                        <tr class="hover:bg-slate-50/50 transition group" 
                            x-show="search === '' || '{{ strtolower($matiere->libelle) }}'.includes(search.toLowerCase()) || '{{ strtolower($matiere->code) }}'.includes(search.toLowerCase())">
                            <td class="px-10 py-5">
                                <span class="bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg font-black text-[10px] italic border border-amber-100">
                                    {{ $matiere->code }}
                                </span>
                            </td>
                            <td class="px-10 py-5">
                                <span class="block font-black text-slate-800 text-sm italic uppercase tracking-tight">{{ $matiere->libelle }}</span>
                            </td>
                            <td class="px-10 py-5">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $matiere->ue->libelle ?? 'Non assignée' }}</span>
                            </td>
                            <td class="px-10 py-5 text-center">
                                <span class="text-xs font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-full">{{ $matiere->coefficient }}</span>
                            </td>
                            <td class="px-10 py-5 text-center">
                                <span class="text-xs font-black text-slate-400 tracking-tighter">{{ $matiere->credits }} ECTS</span>
                            </td>
                            <td class="px-10 py-5 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEdit({{ $matiere->toJson() }})" class="p-2 text-slate-400 hover:text-amber-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2"/></svg>
                                    </button>
                                    
                                    <form action="{{ route('admin.matieres.destroy', $matiere) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette matière ?')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-slate-400 hover:text-rose-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-10 py-20 text-center">
                                <p class="font-black uppercase tracking-widest text-xs italic opacity-20">Aucune matière trouvée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($matieres->hasPages())
                <div class="px-10 py-6 bg-slate-50/50 border-t border-slate-100">{{ $matieres->links() }}</div>
            @endif
        </div>

        <x-modal name="add-matiere" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Nouvelle <span class="text-amber-500">Matière</span></h2>
                
                <form action="{{ route('admin.matieres.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code</label>
                            <input type="text" name="code" required placeholder="ex: ASUR-501" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">UE</label>
                            <select name="ue_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                                <option value="" disabled selected>Choisir une UE...</option>
                                @foreach($ues as $ue)
                                    <option value="{{ $ue->id }}">{{ $ue->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" required placeholder="ex: Administration Système Linux" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500 uppercase italic">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coefficient</label>
                            <input type="number" name="coefficient" value="1" min="1" required placeholder="ex: 2" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Crédits (ECTS)</label>
                            <input type="number" name="credits" value="1" min="1" required placeholder="ex: 4" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-100 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg">Enregistrer</button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="edit-matiere" focusable>
            <div class="p-10">
                <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-8">Modifier <span class="text-amber-500" x-text="currentMatiere.libelle"></span></h2>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code</label>
                            <input type="text" name="code" :value="currentMatiere.code" required placeholder="Modifier le code..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">UE</label>
                            <select name="ue_id" :value="currentMatiere.ue_id" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs">
                                @foreach($ues as $ue)
                                    <option value="{{ $ue->id }}">{{ $ue->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" :value="currentMatiere.libelle" required placeholder="Modifier le libellé..." class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500 uppercase italic">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coefficient</label>
                            <input type="number" name="coefficient" :value="currentMatiere.coefficient" min="1" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Crédits</label>
                            <input type="number" name="credits" :value="currentMatiere.credits" min="1" required class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-100 transition-all">Annuler</button>
                        <button type="submit" class="px-8 py-4 bg-amber-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>