<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-[10px] font-black uppercase tracking-widest">
                        <li class="text-slate-400">Administration</li>
                        <li><svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg></li>
                        <li class="text-emerald-600 italic uppercase">Unités d'Enseignement</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Blocs <span class="text-emerald-600 underline decoration-emerald-100 underline-offset-8">Pédagogiques</span></h1>
            </div>

            <button @click="$dispatch('open-modal', 'add-ue')" class="inline-flex items-center justify-center px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <svg class="w-5 h-5 transform group-hover:scale-110 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Nouvelle UE
            </button>
        </div>
    </x-slot>

    <div class="space-y-8 animate-fade-in" x-data="{ 
        currentUe: { id: '', code: '', libelle: '', semestre_id: '', coefficient: 1, credits: 0 }, 
        editAction: '',
        openEdit(ue) {
            this.currentUe = { ...ue };
            this.editAction = '/admin/ues/' + ue.id;
            $dispatch('open-modal', 'edit-ue');
        }
    }">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="px-10 py-6">Code & Désignation</th>
                        <th class="px-10 py-6">Semestre</th>
                        <th class="px-10 py-6 text-center">Structure</th>
                        <th class="px-10 py-6 text-center">Poids (Coeff/ECTS)</th>
                        <th class="px-10 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($ues as $ue)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-10 py-5">
                            <div class="flex flex-col">
                                <span class="text-emerald-600 font-black text-[10px] uppercase tracking-widest italic mb-1">{{ $ue->code }}</span>
                                <span class="font-bold text-slate-900 uppercase tracking-tight">{{ $ue->libelle }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-5">
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg font-black text-[10px] uppercase italic">
                                {{ $ue->semestre->libelle ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-10 py-5 text-center">
                            <span class="text-[10px] font-black px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full uppercase tracking-tighter">
                                {{ $ue->matieres_count ?? 0 }} matière(s)
                            </span>
                        </td>
                        <td class="px-10 py-5 text-center">
                            <div class="inline-flex items-center gap-3">
                                <span class="text-slate-900 font-black">×{{ $ue->coefficient }}</span>
                                <span class="w-px h-4 bg-slate-200"></span>
                                <span class="text-emerald-500 font-black tracking-tighter">{{ $ue->credits }} ECTS</span>
                            </div>
                        </td>
                        <td class="px-10 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <button @click="openEdit({{ json_encode($ue) }})" class="p-2.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" /></svg>
                                </button>
                                
                                <form action="{{ route('admin.ues.destroy', $ue) }}" method="POST" onsubmit="return confirm('Supprimer cette Unité d\'Enseignement ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center opacity-20 font-black uppercase text-xs tracking-[0.4em] italic text-slate-400">Aucune UE configurée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ues->hasPages())
            <div class="px-2">{{ $ues->links() }}</div>
        @endif

        {{-- MODALE CRÉATION --}}
        <x-modal name="add-ue" focusable>
            <div class="p-10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Nouveau <span class="text-emerald-600 italic">Bloc UE</span></h2>
                    <button x-on:click="$dispatch('close')" class="text-slate-300 hover:text-rose-600 transition-colors uppercase font-black text-[10px]">Fermer</button>
                </div>
                
                <form action="{{ route('admin.ues.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code Identifiant</label>
                            <input type="text" name="code" required placeholder="Ex: UE11" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Semestre</label>
                            <select name="semestre_id" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                                @foreach($semestres as $s)
                                    <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Désignation complète</label>
                        <input type="text" name="libelle" required placeholder="Ex: Algorithmique et Programmation" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coefficient</label>
                            <input type="number" name="coefficient" value="1" min="1" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Crédits ECTS</label>
                            <input type="number" name="credits" value="0" min="0" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-50">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Annuler</button>
                        <button type="submit" class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-emerald-50">Enregistrer l'UE</button>
                    </div>
                </form>
            </div>
        </x-modal>

        {{-- MODALE ÉDITION --}}
        <x-modal name="edit-ue" focusable>
            <div class="p-10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter">Modifier <span class="text-emerald-600 italic" x-text="currentUe.code"></span></h2>
                    <button x-on:click="$dispatch('close')" class="text-slate-300 hover:text-rose-600 transition-colors uppercase font-black text-[10px]">Fermer</button>
                </div>
                
                <form :action="editAction" method="POST" class="space-y-6">
                    @csrf @method('PUT')
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code</label>
                            <input type="text" name="code" x-model="currentUe.code" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Semestre</label>
                            <select name="semestre_id" x-model="currentUe.semestre_id" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                                @foreach($semestres as $s)
                                    <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                        <input type="text" name="libelle" x-model="currentUe.libelle" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coeff</label>
                            <input type="number" name="coefficient" x-model="currentUe.coefficient" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">ECTS</label>
                            <input type="number" name="credits" x-model="currentUe.credits" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-sm focus:ring-2 focus:ring-emerald-500 transition-all">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-slate-50">
                        <button type="button" x-on:click="$dispatch('close')" class="px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-colors">Abandonner</button>
                        <button type="submit" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>