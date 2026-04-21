<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentFiliere: { id: '', nom: '', departement_id: '' },
        openEditModal(filiere) {
            this.currentFiliere = filiere;
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Filières d'Études</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Gestion des spécialisations académiques de l'INPTIC</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold text-sm hover:bg-emerald-700 transition shadow-lg shadow-emerald-100 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round"/></svg>
                    Ajouter une Filière
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Nom de la Filière</th>
                        <th class="px-8 py-5">Département Parent</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($filieres as $filiere)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-slate-900 uppercase italic tracking-tight">{{ $filiere->nom }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg font-black text-[10px] uppercase">
                                {{ $filiere->departement->nom ?? 'Non classé' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ json_encode($filiere) }})" class="p-2 text-slate-400 hover:text-emerald-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </button>
                                <form action="{{ route('admin.filieres.destroy', $filiere) }}" method="POST" onsubmit="return confirm('Supprimer cette filière ?')">
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
                {{ $filieres->links() }}
            </div>
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Nouvelle Filière</h2>
                    </div>
                    <form action="{{ route('admin.filieres.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom de la filière</label>
                            <input type="text" name="nom" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Ex: Génie Informatique">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Département</label>
                            <select name="departement_id" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-emerald-500 focus:ring-0 rounded-2xl font-bold text-sm">
                                <option value="">Choisir un département...</option>
                                @foreach($departements as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition shadow-lg mt-4">
                            Enregistrer la filière
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>