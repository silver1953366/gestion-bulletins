<x-app-layout>
    <div x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentDept: { id: '', nom: '' },
        openEditModal(dept) {
            this.currentDept = Object.assign({}, dept);
            this.showEditModal = true;
        }
    }">
        <x-slot name="header">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Départements</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Unités de formation et de recherche de l'INPTIC</p>
                </div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 text-white rounded-2xl font-bold text-sm hover:bg-black transition shadow-lg gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Nouveau Département
                </button>
            </div>
        </x-slot>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5">Nom du Département</th>
                        <th class="px-8 py-5">Nombre de Filières</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                    @foreach($departements as $dept)
                    <tr class="hover:bg-slate-50/50 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-slate-900 uppercase italic">{{ $dept->nom }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-sky-50 text-sky-600 rounded-lg font-black text-[10px] uppercase">
                                {{ $dept->filieres_count }} Filière(s)
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ json_encode($dept) }})" class="p-2 text-slate-400 hover:text-sky-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" /></svg>
                                </button>
                                <form action="{{ route('admin.departements.destroy', $dept) }}" method="POST" onsubmit="return confirm('Supprimer ce département ? Cela échouera s\'il contient des filières.')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-slate-300 hover:text-rose-600 transition">
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
                {{ $departements->links() }}
            </div>
        </div>

        <template x-if="showCreateModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showCreateModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Créer un Département</h2>
                    </div>
                    <form action="{{ route('admin.departements.store') }}" method="POST" class="p-10 space-y-6">
                        @csrf
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom (ex: Informatique)</label>
                            <input type="text" name="nom" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-slate-900 focus:ring-0 rounded-2xl font-bold text-sm" placeholder="Nom du département">
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showCreateModal = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition">Annuler</button>
                            <button type="submit" class="flex-[2] py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition shadow-lg">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template x-if="showEditModal">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="showEditModal = false" class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in">
                    <div class="px-10 py-8 border-b border-slate-100 bg-sky-50/50">
                        <h2 class="text-2xl font-black text-slate-900 italic">Modifier Département</h2>
                    </div>
                    <form :action="'{{ route('admin.departements.index') }}/' + currentDept.id" method="POST" class="p-10 space-y-6">
                        @csrf 
                        @method('PUT')
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nom du département</label>
                            <input type="text" name="nom" x-model="currentDept.nom" required class="w-full px-5 py-4 bg-slate-50 border-transparent focus:border-slate-900 focus:ring-0 rounded-2xl font-bold text-sm">
                        </div>
                        <div class="flex gap-3">
                            <button type="button" @click="showEditModal = false" class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition">Annuler</button>
                            <button type="submit" class="flex-[2] py-4 bg-sky-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-sky-700 transition shadow-lg">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>