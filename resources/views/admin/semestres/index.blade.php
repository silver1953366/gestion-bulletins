<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentSemestre: { id: '', libelle: '', classe_id: '' },
        openEditModal(semestre) {
            this.currentSemestre = semestre;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Périodes & Semestres</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Organisation temporelle des formations</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-amber-500 text-white rounded-2xl font-bold text-sm hover:bg-amber-600 transition shadow-lg shadow-amber-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round"/></svg>
                    Nouveau Semestre
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Intitulé</th>
                        <th class="px-8 py-5">Classe / Formation</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($semestres as $semestre)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-amber-600 uppercase italic">{{ $semestre->libelle }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-bold text-xs">
                                {{ $semestre->classe->nom ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ json_encode($semestre) }})" class="p-2 text-slate-400 hover:text-amber-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </button>
                                <form action="{{ route('admin.semestres.destroy', $semestre) }}" method="POST" onsubmit="return confirm('Supprimer ce semestre ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-400 hover:text-rose-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100">
                {{ $semestres->links() }}
            </div>
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Nouveau Semestre</h2>
                    </div>
                    <form action="{{ route('admin.semestres.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Libellé</label>
                            <input type="text" name="libelle" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-amber-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: Semestre 5">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Assigner à une Classe</label>
                            <select name="classe_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-amber-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                <option value="">Choisir la classe...</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-600 transition shadow-lg mt-4">
                            Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>