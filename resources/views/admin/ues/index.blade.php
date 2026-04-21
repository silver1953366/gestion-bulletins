<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentUe: { id: '', code: '', libelle: '', semestre_id: '', coefficient: 1, credits: 0 },
        openEditModal(ue) {
            this.currentUe = { ...ue }; // Clone pour éviter la modification en direct du tableau
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Unités d'Enseignement</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Structure des blocs de compétences (UE)</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                    Nouvelle UE
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Code UE</th>
                        <th class="px-8 py-5">Désignation</th>
                        <th class="px-8 py-5">Semestre</th>
                        <th class="px-8 py-5 text-center">Contenu</th>
                        <th class="px-8 py-5 text-center">Coeff / ECTS</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @forelse($ues as $ue)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg font-black text-xs uppercase">
                                {{ $ue->code }}
                            </span>
                        </td>
                        <td class="px-8 py-5 font-bold text-slate-700">{{ $ue->libelle }}</td>
                        <td class="px-8 py-5 text-xs font-bold text-slate-400 uppercase italic">
                            {{ $ue->semestre->libelle ?? 'N/A' }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="text-[10px] font-black px-2 py-1 bg-slate-100 text-slate-500 rounded-md uppercase">
                                {{ $ue->matieres_count ?? 0 }} matières
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex flex-col">
                                <span class="text-slate-900 font-black">Coeff: {{ $ue->coefficient }}</span>
                                <span class="text-[10px] text-emerald-500 font-black uppercase tracking-tighter">{{ $ue->credits }} ECTS</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ json_encode($ue) }})" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </button>
                                <form action="{{ route('admin.ues.destroy', $ue) }}" method="POST" onsubmit="return confirm('Supprimer cette UE ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-400 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-12 text-center text-slate-400 font-medium italic">Aucune UE enregistrée pour le moment.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($ues->hasPages())
                <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $ues->links() }}
                </div>
            @endif
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-100 bg-emerald-50/30">
                        <h2 class="text-2xl font-black text-slate-900 italic text-center">Ajouter une UE</h2>
                    </div>
                    <form action="{{ route('admin.ues.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code UE</label>
                                <input type="text" name="code" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: UE51">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Semestre</label>
                                <select name="semestre_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                    @foreach($semestres as $s)
                                        <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                            <input type="text" name="libelle" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: Enseignements Transversaux">
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coefficient</label>
                                <input type="number" name="coefficient" value="1" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Crédits ECTS</label>
                                <input type="number" name="credits" value="0" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg mt-4 shadow-emerald-100">
                            Enregistrer l'UE
                        </button>
                    </form>
                </div>
            </div>
        </template>

        <template x-if="showEditModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fade-in">
                <div @click.away="showEditModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-100 bg-indigo-50/30">
                        <h2 class="text-2xl font-black text-slate-900 italic">Modifier l'UE : <span x-text="currentUe.code" class="text-indigo-600"></span></h2>
                    </div>
                    <form :action="`/admin/ues/${currentUe.id}`" method="POST" class="p-10 space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Code UE</label>
                                <input type="text" name="code" x-model="currentUe.code" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Semestre</label>
                                <select name="semestre_id" x-model="currentUe.semestre_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                    @foreach($semestres as $s)
                                        <option value="{{ $s->id }}">{{ $s->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                            <input type="text" name="libelle" x-model="currentUe.libelle" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Coefficient</label>
                                <input type="number" name="coefficient" x-model="currentUe.coefficient" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Crédits ECTS</label>
                                <input type="number" name="credits" x-model="currentUe.credits" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-indigo-500 focus:ring-0 rounded-2xl font-bold text-sm">
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <button type="button" @click="showEditModal = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition">Annuler</button>
                            <button type="submit" class="flex-1 py-4 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>